<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Billing;
use App\Form\UpdateUsernameProfileType;
use App\Form\UpdateEmailProfileType;
use App\Form\UpdatePasswordProfileType;
use App\Form\BillingProfileType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\CaptchaController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\Form;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/profile")
 */
class ProfileController extends AbstractController
{
    private $passwordEncoder;
    private $captcha;
    private $session;
    private $translator;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder, CaptchaController $captcha, SessionInterface $session, TranslatorInterface $translator)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->captcha = $captcha;
        $this->session = $session;
        $this->translator = $translator;
    }

    /**
     * @Route("", 
     * name="profile",
     * requirements={
     *      "_locale": "en|fr",
     * },
     * methods={"GET", "POST"})
     */
    public function index(Request $request): Response
    {
        // init objects
        $user = new User();
        $repo = $this->getDoctrine()->getRepository(Billing::class);
        $billing = $repo->findOneBy([
            "user_id" => $this->getUser()->getId()
        ]);

        $billingEnabled = $this->getUser()->getIsActiveInvoices();

        if(!$billing){
            $billing = new Billing();
        }

        // create form change username
        $UpdateUsernameForm = $this->createForm(UpdateUsernameProfileType::class, $user);
        $UpdateEmailForm = $this->createForm(UpdateEmailProfileType::class, $user);
        $UpdatePasswordForm = $this->createForm(UpdatePasswordProfileType::class, $user);
        $BillingForm = $this->createForm(BillingProfileType::class, $billing);

        // handle requests
        $UpdateUsernameForm->handleRequest($request);
        $UpdateEmailForm->handleRequest($request);
        $UpdatePasswordForm->handleRequest($request);
        $BillingForm->handleRequest($request);

        // Form sended
        if ($request->isMethod('post')) {

            // Verify captcha
            $captchaToken = $request->get('g-recaptcha-response');
            if($this->captcha->verify($captchaToken)){

                /**
                 * Check for update username form
                 */
                if ($UpdateUsernameForm->isSubmitted() && $UpdateUsernameForm->isValid()) {
                    if($this->formUpdateUsername($UpdateUsernameForm, $user)){
                        return $this->redirectToRoute('profile');
                    }
                }

                /**
                 * Check for update email form
                 */
                if ($UpdateEmailForm->isSubmitted() && $UpdateEmailForm->isValid()) {
                    if($this->formUpdateEmail($UpdateEmailForm, $user)){
                        return $this->redirectToRoute('profile');
                    }
                }

                /**
                 * Check for update password form
                 */
                if ($UpdatePasswordForm->isSubmitted() && $UpdatePasswordForm->isValid()) {
                    if($this->formUpdatePassword($UpdatePasswordForm, $user)){
                        return $this->redirectToRoute('profile');
                    }
                }

                /**
                 * Check for billing form
                 */
                if ($BillingForm->isSubmitted() && $BillingForm->isValid()) {
                    if($this->formBilling($BillingForm, $billing)){
                        return $this->redirectToRoute('profile');
                    }
                } elseif($BillingForm->isSubmitted() && !$BillingForm->isValid()){
                    $this->addFlash("error", $this->translator->trans("The form contains errors, please check the values entered."));
                }
            } else {
                // Captcha failed
                $this->addFlash("error", $this->translator->trans("Sorry, something went wrong. Please, try again."));
            }
        }

        // update user
        return $this->render('profile/index.html.twig', [
            'captcha' => $this->captcha->enabled(),
            'publicKey' => $this->captcha->getPublicKey(),
            "UpdateUsernameForm" => $UpdateUsernameForm->createView(),
            "UpdateEmailForm" => $UpdateEmailForm->createView(),
            "UpdatePasswordForm" => $UpdatePasswordForm->createView(),
            "BillingForm" => $BillingForm->createView(),
            'billing' => $billingEnabled
        ]);
    }

    /**
     * Update username
     * @param UpdateUsernameProfileType : Form request from profile
     * @return true|false
     */
    protected function formUpdateUsername(Form $UpdateUsernameForm, User $user){
        // process to update user

        $repo = $this->getDoctrine()->getRepository(User::class);
        $users = $repo->findAll();

        // check for unicity
        $unicity = true;
        foreach($users as $row){
            // verify that the username doesn't already exist
            if($row->getUsername() == $user->getUsername()){
                $UpdateUsernameForm->get('username')->addError(new FormError($this->translator->trans('This username is already in use')));
                $unicity = false;
            }
        }

        if($unicity){
            // get current user
            $currentUser = $this->getUser();
            $currentUser->setUsername($user->getUsername());

            // save user
            $em = $this->getDoctrine()->getManager();
            $em->persist($currentUser);
            $em->flush();

            // success message
            $this->addFlash('success', $this->translator->trans("You've updated your username."));

            // return
            return true;
        }
        return false;
    }

    /**
     * Update email
     * @param UpdateEmailProfileType : Form request from profile
     * @return true|false
     */
    protected function formUpdateEmail(Form $UpdateEmailForm, User $user){
        // process to update user

        $repo = $this->getDoctrine()->getRepository(User::class);
        $users = $repo->findAll();

        // check for unicity
        $unicity = true;
        foreach($users as $row){
            // verify that the username doesn't already exist
            if($row->getEmail() == $user->getEmail()){
                $UpdateEmailForm->get('email')->addError(new FormError($this->translator->trans('This email is already in use')));
                $unicity = false;
            }
        }

        if($unicity){
            // get current user
            $currentUser = $this->getUser();
            $currentUser->setEmail($user->getEmail());

            // save user
            $em = $this->getDoctrine()->getManager();
            $em->persist($currentUser);
            $em->flush();

            // success message
            $this->addFlash('success', $this->translator->trans("You've updated your email."));

            // return
            return true;
        }
        return false;
    }

    /**
     * Update email
     * @param UpdatePasswordProfileType : Form request from profile
     * @return true|false
     */
    protected function formUpdatePassword(Form $UpdatePasswordForm, User $user){
        // process to update user

        // get current user
        $currentUser = $this->getUser();
        $currentUser->setPassword($user->getPassword());
        $currentUser->setPassword($this->passwordEncoder->encodePassword($currentUser, $currentUser->getPassword()));

        // save user
        $em = $this->getDoctrine()->getManager();
        $em->persist($currentUser);
        $em->flush();

        // success message
        $this->addFlash('success', $this->translator->trans("You've updated your password."));

        // return
        return true;
    }

    /**
     * Enable billing
     * @param BillingProfileType : Form request from profile
     * @return true|false
     */
    protected function formBilling(Form $billingForm, Billing $billing){
        // process to update billing informations
        $billing->setUserId($this->getUser()->getId());

        // update user
        $this->getUser()->setIsActiveInvoices(true);

        // save billing
        $em = $this->getDoctrine()->getManager();
        $em->persist($billing);
        $em->persist($this->getUser());
        $em->flush();


        // success message
        $this->addFlash('success', $this->translator->trans("You've updated your billings informations."));

        // return
        return true;
    }

    /**
     * @Route("/disable-billing", 
     * name="disable-billing",
     * requirements={
     *      "_locale": "en|fr",
     * },
     * methods={"GET"})
     */
    public function disableBilling(){
        // disable billing
        $this->getUser()->setIsActiveInvoices(false);

        // save
        $em = $this->getDoctrine()->getManager();
        $em->persist($this->getUser());
        $em->flush();

        // success message
        $this->addFlash('success', $this->translator->trans("You've disabled billings successfuly."));
        
        // redirect
        return $this->redirectToRoute('profile');
    }

    /**
     * @Route("/reset-billing", 
     * name="reset-billing",
     * requirements={
     *      "_locale": "en|fr",
     * },
     * methods={"GET"})
     */
    public function resetBilling(){
        // create manager
        $em = $this->getDoctrine()->getManager();

        // disable billing
        $this->getUser()->setIsActiveInvoices(false);

        // save user
        $em->persist($this->getUser());

        $repo = $this->getDoctrine()->getRepository(Billing::class);
        $billing = $repo->findOneBy([
            "user_id" => $this->getUser()->getId()
        ]);

        if($billing){
            // delete billing
            $em->remove($billing);
        }

        // save
        $em->flush();

        // success message
        $this->addFlash('success', $this->translator->trans("You've reset billings successfuly."));
        
        // redirect
        return $this->redirectToRoute('profile');
    }
}
