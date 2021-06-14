<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Captcha;
use App\Form\AddUserSettingsType;
use App\Form\UpdateCaptchaFormType;
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
 * @Route("/settings")
 */
class SettingsController extends AbstractController
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
     * name="settings",
     * requirements={
     *      "_locale": "en|fr",
     * },
     * methods={"GET", "POST"})
     */
    public function index(Request $request)
    {
        // init objects
        $user = new User();
        $captchaEntity = new Captcha();

        // get all users
        $repo = $this->getDoctrine()->getRepository(User::class);
        $users = $repo->findAll();

        // create forms
        $AddUserForm = $this->createForm(AddUserSettingsType::class, $user);
        $updateCaptchaForm = $this->createForm(UpdateCaptchaFormType::class, $captchaEntity);

        // handle requests
        $AddUserForm->handleRequest($request);
        $updateCaptchaForm->handleRequest($request);

        // Form sended
        if ($request->isMethod('post')) {

            // Verify captcha
            $captchaToken = $request->get('g-recaptcha-response');
            if($this->captcha->verify($captchaToken)){

                /**
                 * Check for add user form
                 */
                if ($AddUserForm->isSubmitted() && $AddUserForm->isValid()) {
                    if($this->formAddUser($AddUserForm, $user)){
                        return $this->redirectToRoute('settings');
                    }
                }

                /**
                 * check for update captcha form
                 */
                if ($updateCaptchaForm->isSubmitted() && $updateCaptchaForm->isValid()) {
                    
                    if($this->formUpdateCaptha($updateCaptchaForm, $captchaEntity)){
                        // return to settings 
                        return $this->redirectToRoute('settings');
                    }
                }
            } else {
                // Captcha failed
                $this->addFlash("error", $this->translator->trans("Sorry, something went wrong. Please, try again."));
            }
        }
    
        // render page
        return $this->render('settings/index.html.twig', [
            'captcha' => $this->captcha->enabled(),
            'publicKey' => $this->captcha->getPublicKey(),
            'users' => $users,
            "addUserForm" => $AddUserForm->createView(),
            "addCaptchaForm" => $updateCaptchaForm->createView()
        ]);
    }

    /**
     * @Route("/delete-user/{id}", 
     * name="delete-user", 
     * methods={"GET"},
     * requirements={
     *      "_locale": "en|fr",
     * })
     * @param id : Id of user
     * @return redirection
     */
    public function deleteUser(string $id, Request $request)
    {
        // get user
        $repo = $this->getDoctrine()->getRepository(User::class);
        $user = $repo->findOneBy(["id" => $id]);

        // check if exist
        if(!$user){
            // error message
            $this->addFlash("error", $this->translator->trans("Sorry, something went wrong. Please, try again."));

            // return
            return $this->redirectToRoute('settings');
        }
        
        // delete user
        $em = $this->getDoctrine()->getManager();
        $em->remove($user);
        $em->flush();

        // success message
        $this->addFlash("success", $this->translator->trans("The user has been deleted successfuly."));

        // return
        return $this->redirectToRoute('settings');
    }

    /**
     * Update captcha keys
     * @param UpdateCaptchaFormType : Form request from settings
     * @return true
     */
    protected function formUpdateCaptha(Form $updateCaptchaForm, Captcha $captcha){
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // process to update keys
        // check if database already contain keys for captcha
        $repo = $this->getDoctrine()->getRepository(Captcha::class);
        $captchas = $repo->getKeys();

        // if this is the case, update current keys and save
        if($captchas){
            $currentCaptcha = $captchas[0];
            $currentCaptcha->setPrivateKey($captcha->getPrivateKey());
            $currentCaptcha->setPublicKey($captcha->getPublicKey());

            // save user
            $em = $this->getDoctrine()->getManager();
            $em->persist($currentCaptcha);
            $em->flush();
        // else, save new keys
        } else {
            $em = $this->getDoctrine()->getManager();
            $em->persist($captcha);
            $em->flush();
        }

        // success message
        $this->addFlash('success', $this->translator->trans("You've updated captcha keys successfuly."));
        // return
        return true;
    }

    /**
     * Add a new user
     * @param AddUserSettingsType : Form request from settings
     * @return Form
     */
    protected function formAddUser(Form $AddUserForm, User $user){
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // process to add a new user
        $repo = $this->getDoctrine()->getRepository(User::class);
        $users = $repo->findAll();

        // check for unicity
        $unicity = true;
        foreach($users as $row){
            // verify that the email doesn't already exist
            if($row->getEmail() == $user->getEmail()){
                $AddUserForm->get('email')->addError(new FormError($this->translator->trans('This e-mail address is already in use')));
                $unicity = false;
            }

            // verify that the username doesn't already exist
            if($row->getUsername() == $user->getUsername()){
                $AddUserForm->get('username')->addError(new FormError($this->translator->trans('This username is already in use')));
                $unicity = false;
            }
        }

        if($unicity){
            $user->setPassword($this->passwordEncoder->encodePassword($user, $user->getPassword()));
            // update roles
            $roles = array();

            if($AddUserForm->get('admin')->getData()){
                array_push($roles, 'ROLE_ADMIN');
            }
            if($AddUserForm->get('hunter')->getData()){
                array_push($roles, 'ROLE_USER');
            }
            $user->setRoles($roles);
            $user->setIsActive(true);

            // save user
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            // success message
            $this->addFlash('success', $this->translator->trans("You've added a new user"));
            // return
            return true;
        }
        return false;
    }

    /**
     * @Route("/reset-captcha", 
     * name="reset-captcha", 
     * methods={"GET"},
     * requirements={
     *      "_locale": "en|fr",
     * })
     * @return redirection
     */
    public function resetCaptcha(Request $request)
    {
        $this->captcha->resetKeys();

        // success message
        $this->addFlash("success", $this->translator->trans("You have successfully reset the captcha"));

        // return
        return $this->redirectToRoute('settings');
    }
}
