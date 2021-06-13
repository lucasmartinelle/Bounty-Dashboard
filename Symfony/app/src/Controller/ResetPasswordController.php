<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ChangePasswordFormType;
use App\Controller\CaptchaController;
use App\Form\ResetPasswordRequestFormType;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/reset-password")
 */
class ResetPasswordController extends AbstractController
{
    use ResetPasswordControllerTrait;

    private $resetPasswordHelper;
    private $translator;

    public function __construct(ResetPasswordHelperInterface $resetPasswordHelper, CaptchaController $captcha, TranslatorInterface $translator)
    {
        $this->resetPasswordHelper = $resetPasswordHelper;
        $this->captcha = $captcha;
        $this->translator = $translator;
    }

    /**
     * Display & process form to request a password reset.
     *
     * @Route("", name="app_forgot_password_request")
     */
    public function request(Request $request, MailerInterface $mailer): Response
    {
        $form = $this->createForm(ResetPasswordRequestFormType::class);
        $form->handleRequest($request);

        // Form sended
        if ($request->isMethod('post')) {
            // Verify captcha
            $captchaToken = $request->get('g-recaptcha-response');
            if($this->captcha->verify($captchaToken)){
                // Verify form
                if ($form->isSubmitted() && $form->isValid()) {
                    return $this->processSendingPasswordResetEmail(
                        $form->get('email')->getData(),
                        $mailer
                    );
                }
            } else {
                // Captcha failed
                $this->addFlash("error", $this->translator->trans("Sorry, something went wrong. Please, try again."));
                
                return $this->redirectToRoute('app_forgot_password_request');
            }
        }

        return $this->render('reset_password/request.html.twig', [
            'requestForm' => $form->createView(),
            'captcha' => $this->captcha->enabled(),
            'publicKey' => $this->captcha->getPublicKey()
        ]);
    }

    /**
     * Confirmation page after a user has requested a password reset.
     *
     * @Route("/check-email", name="app_check_email")
     */
    public function checkEmail(): Response
    {
        return $this->render('reset_password/check_email.html.twig', []);
    }

    /**
     * Validates and process the reset URL that the user clicked in their email.
     *
     * @Route("/reset/{token}", name="app_reset_password")
     */
    public function reset(Request $request, UserPasswordEncoderInterface $passwordEncoder, string $token = null): Response
    {
        if ($token) {
            // We store the token in session and remove it from the URL, to avoid the URL being
            // loaded in a browser and potentially leaking the token to 3rd party JavaScript.
            $this->storeTokenInSession($token);

            return $this->redirectToRoute('app_reset_password');
        }

        $token = $this->getTokenFromSession();
        if (null === $token) {
            throw $this->createNotFoundException($this->translator->trans('No reset password token found in the URL or in the session.'));
        }

        try {
            $user = $this->resetPasswordHelper->validateTokenAndFetchUser($token);
        } catch (ResetPasswordExceptionInterface $e) {
            $this->addFlash('error', $this->translator->trans('Sorry, something went wrong. Please, try again.'));

            return $this->redirectToRoute('app_forgot_password_request');
        }

        // The token is valid; allow the user to change their password.
        $form = $this->createForm(ChangePasswordFormType::class);
        $form->handleRequest($request);

        // Form sended
        if ($request->isMethod('post')) {
            // Verify captcha
            $captchaToken = $request->get('g-recaptcha-response');
            if($this->captcha->verify($captchaToken)){
                // Verify form
                if ($form->isSubmitted() && $form->isValid()) {
                    // A password reset token should be used only once, remove it.
                    $this->resetPasswordHelper->removeResetRequest($token);

                    // Encode the plain password, and set it.
                    $encodedPassword = $passwordEncoder->encodePassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    );

                    $user->setPassword($encodedPassword);
                    $this->getDoctrine()->getManager()->flush();

                    // The session is cleaned up after the password has been changed.
                    $this->cleanSessionAfterReset();

                    $this->addFlash("success", $this->translator->trans("Your password has been changed."));

                    return $this->redirectToRoute('login');
                }
            } else {
                // Captcha failed
                $this->addFlash("error", $this->translator->trans("Sorry, something went wrong. Please, try again."));
                                
                return $this->redirectToRoute('app_forgot_password_request');
            }
        }

        return $this->render('reset_password/reset.html.twig', [
            'resetForm' => $form->createView(),
            'captcha' => $this->captcha->enabled(),
            'publicKey' => $this->captcha->getPublicKey()
        ]);
    }

    private function processSendingPasswordResetEmail(string $emailFormData, MailerInterface $mailer): RedirectResponse
    {
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy([
            'email' => $emailFormData,
        ]);

        // Do not reveal whether a user account was found or not.
        if (!$user) {
            return $this->redirectToRoute('app_check_email');
        }

        try {
            $resetToken = $this->resetPasswordHelper->generateResetToken($user);
        } catch (ResetPasswordExceptionInterface $e) {
            return $this->redirectToRoute('app_check_email');
        }

        $email = (new TemplatedEmail())
            ->from(new Address('lucaspro.martinelle@gmail.com', 'Lucas Martinelle'))
            ->to($user->getEmail())
            ->subject($this->translator->trans('Your password reset request'))
            ->htmlTemplate('reset_password/email.html.twig')
            ->context([
                'resetToken' => $resetToken,
            ])
        ;

        $mailer->send($email);

        // Store the token object in session for retrieval in check-email route.
        $this->setTokenObjectInSession($resetToken);

        return $this->redirectToRoute('app_check_email');
    }
}
