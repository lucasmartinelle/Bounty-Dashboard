<?php

namespace App\Controller;

use App\Entity\Templates;
use App\Form\CreateTemplateTemplatesType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Controller\CaptchaController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * @Route("/templates")
 */
class TemplatesController extends AbstractController
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
     * @Route("", name="templates")
     */
    public function index(): Response
    {
        // get templates
        $repo = $this->getDoctrine()->getRepository(Templates::class);
        $templates = $repo->findAll();

        return $this->render('templates/index.html.twig', [
            'captcha' => $this->captcha->enabled(),
            'publicKey' => $this->captcha->getPublicKey(),
            'templates' => $templates
        ]);
    }

    /**
     * @Route("/create", name="create-template")
     */
    public function createTemplate(Request $request): Response {
        // init object
        $template = new Templates();

        // create form create template
        $CreateTemplateForm = $this->createForm(CreateTemplateTemplatesType::class, $template);

        // handle requests
        $CreateTemplateForm->handleRequest($request);

        // Form sended
        if ($request->isMethod('post')) {

            // Verify captcha
            $captchaToken = $request->get('g-recaptcha-response');
            if($this->captcha->verify($captchaToken)){
                if ($CreateTemplateForm->isSubmitted() && $CreateTemplateForm->isValid()) {
                    // data valid
                    $template->setCreatorId($this->getUser()->getId());

                    // save
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($template);
                    $em->flush();

                    // success message
                    $this->addFlash("success", $this->translator->trans("The template was successfully created."));

                    // return
                    return $this->redirectToRoute('templates');
                }
            } else {
                // Captcha failed
                $this->addFlash("error", $this->translator->trans("Sorry, something went wrong. Please, try again."));
            }
        }
        
        return $this->render('templates/create.html.twig', [
            'captcha' => $this->captcha->enabled(),
            'publicKey' => $this->captcha->getPublicKey(),
            'CreateTemplateForm' => $CreateTemplateForm->createView()
        ]);
    }

    /**
     * @Route("/delete/{id}", name="delete-template")
     */
    public function deleteTemplate(string $id): Response {
        // get template by id
        $repo = $this->getDoctrine()->getRepository(Templates::class);
        $template = $repo->findOneBy([
            "id" => $id
        ]);

        if($template){
            // delete template
            $em = $this->getDoctrine()->getManager();
            $em->remove($template);
            $em->flush();

            // success message
            $this->addFlash("success", $this->translator->trans("The template was successfully deleted."));

            // redirect
            return $this->redirectToRoute('templates');
        } else {
            $this->addFlash("error", $this->translator->trans("Sorry, the template cannot be found."));
            return $this->redirectToRoute('templates');
        }
    }

    /**
     * @Route("/show/{id}", name="show-template")
     */
    public function showTemplate(string $id): Response {
        // get template by id
        $repo = $this->getDoctrine()->getRepository(Templates::class);
        $template = $repo->findOneBy([
            "id" => $id
        ]);

        if($template){
            return $this->render('templates/show.html.twig', [
                'captcha' => $this->captcha->enabled(),
                'publicKey' => $this->captcha->getPublicKey(),
                'template' => $template,
            ]);
        } else {
            $this->addFlash("error", $this->translator->trans("Sorry, the template cannot be found."));
            return $this->redirectToRoute('templates');
        }
    }

    /**
     * @Route("/edit/{id}", name="edit-template")
     */
    public function editTemplates(string $id, Request $request): Response {
        // get template by id
        $repo = $this->getDoctrine()->getRepository(Templates::class);
        $template = $repo->findOneBy([
            "id" => $id
        ]);

        if($template){
            // create form edit template
            $EditTemplateForm = $this->createForm(CreateTemplateTemplatesType::class, $template);

            // handle requests
            $EditTemplateForm->handleRequest($request);

            // Form sended
            if ($request->isMethod('post')) {

                // Verify captcha
                $captchaToken = $request->get('g-recaptcha-response');
                if($this->captcha->verify($captchaToken)){
                    /**
                     * Edit template form sended
                     */
                    if ($EditTemplateForm->isSubmitted() && $EditTemplateForm->isValid()) {
                        // save
                        $em = $this->getDoctrine()->getManager();
                        $em->persist($template);
                        $em->flush();

                        // success message
                        $this->addFlash("success", $this->translator->trans("The template was successfully edited."));

                        // return
                        return $this->redirectToRoute('templates');
                    }
                } else {
                    // Captcha failed
                    $this->addFlash("error", $this->translator->trans("Sorry, something went wrong. Please, try again."));
                }
            }

            return $this->render('templates/edit.html.twig', [
                'captcha' => $this->captcha->enabled(),
                'publicKey' => $this->captcha->getPublicKey(),
                'EditTemplateForm' => $EditTemplateForm->createView()
            ]);
        } else {
            $this->addFlash("error", $this->translator->trans("Sorry, the template cannot be found."));
            return $this->redirectToRoute('reports');
        }
    }
}
