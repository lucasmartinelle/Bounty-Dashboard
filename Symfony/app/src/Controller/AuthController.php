<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Controller\CaptchaController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Contracts\Translation\TranslatorInterface;

class AuthController extends AbstractController
{
    private $passwordEncoder;
    private $captcha;
    private $translator;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder, CaptchaController $captcha, TranslatorInterface $translator)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->captcha = $captcha;
        $this->translator = $translator;
    }

    /**
     * @Route("/", name="login")
     */
    public function login(Request $request,AuthenticationUtils $authenticationUtils)
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('dashboard');
        }

        $user = new User();

        $form = $this->createForm(UserType::class, $user);

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_email' => $lastUsername, 
            'error' => $error, 
            'form' => $form->createView(),
            'captcha' => $this->captcha->enabled(),
            'publicKey' => $this->captcha->getPublicKey()
        ]);
    }

    /**
     * @Route("/sign-up", name="registration")
     */
    public function registration(Request $request, MailerInterface $mailer)
    {
        // create new user
        $user = new User();

        // create form
        $form = $this->createForm(UserType::class, $user);

        // handle request
        $form->handleRequest($request);

        // Form sended
        if ($request->isMethod('post')) {
            // Verify captcha
            $captchaToken = $request->get('g-recaptcha-response');
            if($this->captcha->verify($captchaToken)){
                // Verify form
                if ($form->isSubmitted() && $form->isValid()) {
                    // create repository
                    $repo = $this->getDoctrine()->getRepository(User::class);
                    $users = $repo->findAll();

                    foreach($users as $row){
                        // verify that the email doesn't already exist
                        if($row->getEmail() == $user->getEmail()){
                            // block enumeration of email
                            $this->addFlash("success", $this->translator->trans("Registration successfull, please confirm your email before log in."));
                            
                            return $this->redirectToRoute('login');
                            die;
                        }
                    }

                    // Encode the new users password
                    $user->setPassword($this->passwordEncoder->encodePassword($user, $user->getPassword()));

                    // count user
                    $totalUser = $repo->totalUser();

                    // if there is less than 2 user, attribute role admin
                    if($totalUser < 2){
                        $user->setRoles(['ROLE_ADMIN']);
                    }

                    // Save user
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($user);
                    $em->flush();
    
                    // Send email
                    $token = $user->getToken();
                    $email = $user->getEmail();
                    $username = $user->getUsername();
                    $confirmRegistrationPage = $this->generateUrl('confirmRegistration', [
                        'token' => $token,
                    ],UrlGeneratorInterface::ABSOLUTE_URL);
    
                    try {
                        $email = (new TemplatedEmail())
                            ->from('lucaspro.martinelle@gmail.com')
                            ->to($email)
                            ->priority(Email::PRIORITY_HIGH)
                            ->subject($this->translator->trans('Thank you for your registration'))
                            ->htmlTemplate('emails/signup.html.twig')
                            ->context([
                                'username' => $username,
                                'url' => $confirmRegistrationPage
                            ]);
    
                        $mailer->send($email);
                    } catch (TransportExceptionInterface $e) {
                        $this->addFlash("error", $this->translator->trans("Sorry, something went wrong. Please, try again."));
                    
                        return $this->redirectToRoute('registration');
                    }
    
                    // Registration success
                    $this->addFlash("success", $this->translator->trans("Registration successfull, please confirm your email before log in."));
                    
                    return $this->redirectToRoute('login');
                }
            } else {
                // Captcha failed
                $this->addFlash("error", $this->translator->trans("Sorry, something went wrong. Please, try again."));
                
                return $this->redirectToRoute('registration');
            }
        }

        // Render page
        return $this->render('registration/index.html.twig', [
            'form' => $form->createView(),
            'captcha' => $this->captcha->enabled(),
            'publicKey' => $this->captcha->getPublicKey()
        ]);
    }

    /**
     * @Route("/registration/confirm/{token}", name="confirmRegistration")
     */
    public function confirmRegistration(string $token){
        // get user with token
        $repo = $this->getDoctrine()->getRepository(User::class);
        $user = $repo->findOneBy(["token" => $token]);
        $em = $this->getDoctrine()->getManager();

        // user don't exist
        if (!$user) {
            $this->addFlash("error", $this->translator->trans("Sorry, this user is unknown."));
            
            return $this->redirectToRoute('registration');
        // user exist
        } else {
            // update user
            $user->setIsActive(true);
            $repo->upgradeToken($user);
            $em->persist($user);
            $em->flush();

            // confirmation success
            $this->addFlash("success", $this->translator->trans("Thank you ! You account has been enabled."));
            
            return $this->redirectToRoute('registration');
        }
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout()
    {
        // route intercept by firewall
    }
}
