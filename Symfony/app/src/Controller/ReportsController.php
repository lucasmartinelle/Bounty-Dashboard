<?php

namespace App\Controller;

use App\Entity\Reports;
use App\Entity\Programs;
use App\Entity\Platforms;
use App\Entity\Templates;
use App\Form\CreateReportReportsType;
use App\Form\UseTemplateReportsType;
use App\Form\FiltersReportsType;
use App\Form\GainReportsType;
use App\Form\StatusReportsType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Controller\CaptchaController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpKernel\KernelInterface;
use Knp\Snappy\Pdf;

/**
 * @Route("/reports")
 */
class ReportsController extends AbstractController
{
    private $passwordEncoder;
    private $captcha;
    private $session;
    private $translator;
    private $kernel;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder, CaptchaController $captcha, SessionInterface $session, TranslatorInterface $translator, KernelInterface $kernel)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->captcha = $captcha;
        $this->session = $session;
        $this->translator = $translator;
        $this->kernel = $kernel;
    }
    /**
     * @Route("", name="reports")
     */
    public function index(Request $request): Response
    {
        $watchall = $this->session->get('_watch_all', false);

        if($watchall){
            // get reports
            $repo = $this->getDoctrine()->getRepository(Reports::class);
            $reports = $repo->findAll();
        } else {
            $repo = $this->getDoctrine()->getRepository(Reports::class);
            $reports = $repo->findBy([
                "creator_id" => $this->getUser()->getId()
            ]);
        }

        // get programs names
        $repo = $this->getDoctrine()->getRepository(Programs::class);
        $programs = $repo->findAll();
        $programsName = array();
        foreach($programs as $row){
            $programsName[$row->getName()] = $row->getId();
        }

        // get platforms names
        $repo = $this->getDoctrine()->getRepository(Platforms::class);
        $platforms = $repo->findAll();
        $platformsName = array();
        foreach($platforms as $row){
            $platformsName[$row->getName()] = $row->getId();
        }

        // create filters form
        $FiltersForm = $this->createForm(FiltersReportsType::class, null, [
            "programsName" => $programsName,
            "platformsName" => $platformsName
        ]);

        // create update gain form
        $UpdateGainForm = $this->createForm(GainReportsType::class);

        // create update status form
        $UpdateStatusForm = $this->createForm(StatusReportsType::class);

        // handle request
        $FiltersForm->handleRequest($request);
        $UpdateGainForm->handleRequest($request);
        $UpdateStatusForm->handleRequest($request);

        // Form sended
        if ($request->isMethod('post')) {

            // Verify captcha
            $captchaToken = $request->get('g-recaptcha-response');
            if($this->captcha->verify($captchaToken)){
                /**
                 * Filters form sended
                 */
                if ($FiltersForm->isSubmitted() && $FiltersForm->isValid()) {
                    // filters form
                    $filters = $this->formFilters($FiltersForm, $watchall);

                    if($filters !== false){
                        $reports = $filters;
                    } else {
                        $this->addFlash("error", $this->translator->trans("The form contains errors, please check the values entered."));
                    }
                } elseif($FiltersForm->isSubmitted() && !$FiltersForm->isValid()){
                    // error message
                    $this->addFlash("error", $this->translator->trans("The form contains errors, please check the values entered."));
                }

                /**
                 * Update gain form sended
                 */
                if ($UpdateGainForm->isSubmitted() && $UpdateGainForm->isValid()) {
                    // update gain form
                    if($this->formUpdateGain($UpdateGainForm)){
                        $this->addFlash("success", $this->translator->trans("The report gain has been successfully updated."));
                    }

                    return $this->redirectToRoute('reports');
                } elseif($UpdateGainForm->isSubmitted() && !$UpdateGainForm->isValid()){
                    // error message
                    $this->addFlash("error", $this->translator->trans("The form contains errors, please check the values entered."));
                }
                
                /**
                 * Update status form sended
                 */
                if ($UpdateStatusForm->isSubmitted() && $UpdateStatusForm->isValid()) {
                    // update status form
                    if($this->formUpdateStatus($UpdateStatusForm)){
                        $this->addFlash("success", $this->translator->trans("The report status has been successfully updated."));
                    }

                    return $this->redirectToRoute('reports');
                } elseif($UpdateStatusForm->isSubmitted() && !$UpdateStatusForm->isValid()){
                    // error message
                    $this->addFlash("error", $this->translator->trans("The form contains errors, please check the values entered."));
                }
            } else {
                $this->addFlash("error", $this->translator->trans("Sorry, something went wrong. Please, try again."));
            }
        }

        return $this->render('reports/index.html.twig', [
            'captcha' => $this->captcha->enabled(),
            'publicKey' => $this->captcha->getPublicKey(),
            'reports' => $reports,
            'FiltersForm' => $FiltersForm->createView(),
            'UpdateGainForm' => $UpdateGainForm->createView(),
            'UpdateStatusForm' => $UpdateStatusForm->createView()
        ]);
    }

    /**
     * Filters form
     * @param FiltersReportsType : Form request from reports
     * @param watchall : Select all report or only report of user ?
     * @return object|false
     */
    protected function formFilters(Form $FiltersForm, $watchall){
        // get form fields
        $program = $FiltersForm->get('program')->getData();
        $platform = $FiltersForm->get('platform')->getData();
        $status = $FiltersForm->get('status')->getData();
        $severity_min = $FiltersForm->get('severity_min')->getData();
        $severity_max = $FiltersForm->get('severity_max')->getData();

        // check that severity_min and severity_max is between 0 and 10
        if($severity_min > 10.0 or $severity_min < 0.0){
            $FiltersForm->get('severity_min')->addError(new FormError($this->translator->trans('The severity must be between 0.0 and 10.0')));
            return false;
        }

        if($severity_max > 10.0 or $severity_max < 0.0){
            $FiltersForm->get('severity_max')->addError(new FormError($this->translator->trans('The severity must be between 0.0 and 10.0')));
            return false;
        }

        // if there is at least one value filled
        if($program || $platform || $status || $severity_max || $severity_min){
            // return find like in report repository
            $repo = $this->getDoctrine()->getRepository(Reports::class);
            if($watchall){
                return $repo->filters($program, $platform, $status, $severity_max, $severity_min);
            } else {
                return $repo->filters($program, $platform, $status, $severity_max, $severity_min, $this->getUser()->getId());
            }
        }

        // return
        return false;
    }

    /**
     * Update gain form
     * @param GainReportsType : Form request from reports
     * @return true
     */
    protected function formUpdateGain(Form $UpdateGainForm){
        // get form fields
        $idReport = $UpdateGainForm->get('id')->getData();
        $gain = $UpdateGainForm->get('gain')->getData();
        
        // get report
        $repo = $this->getDoctrine()->getRepository(Reports::class);
        $report = $repo->findOneBy([
            "id" => $idReport
        ]);

        // update gain
        $report->setGain($gain);

        // save
        $em = $this->getDoctrine()->getManager();
        $em->persist($report);
        $em->flush();

        // return
        return true;
    }

    /**
     * Update status form
     * @param StatusReportsType : Form request from reports
     * @return true
     */
    protected function formUpdateStatus(Form $UpdateStatusForm){
        // get form fields
        $idReport = $UpdateStatusForm->get('id')->getData();
        $status = $UpdateStatusForm->get('status')->getData();
        
        // get report
        $repo = $this->getDoctrine()->getRepository(Reports::class);
        $report = $repo->findOneBy([
            "id" => $idReport
        ]);

        // update gain
        $report->setStatus($status);

        // save
        $em = $this->getDoctrine()->getManager();
        $em->persist($report);
        $em->flush();

        // return
        return true;
    }

    /**
     * @Route("/create", name="create-report")
     */
    public function createReport(Request $request): Response {
        // init object
        $report = new Reports();

        // get templates names
        $repo = $this->getDoctrine()->getRepository(Templates::class);
        $templates = $repo->findAll();
        $templatesName = array();
        foreach($templates as $row){
            $templatesName[$row->getTitle()] = $row->getId();
        }

        // create form use template
        $UseTemplateForm = $this->createForm(UseTemplateReportsType::class, null, [
            "templatesName" => $templatesName
        ]);

        // handle requests
        $UseTemplateForm->handleRequest($request);

        // Form sended
        if ($request->isMethod('post') && $UseTemplateForm->isSubmitted()) {

            // Verify captcha
            $captchaToken = $request->get('g-recaptcha-response');
            if($this->captcha->verify($captchaToken)){
                /**
                 * Use template form sended
                 */
                if ($UseTemplateForm->isValid()) {
                    // data valid
                    // get template
                    $templateId = $UseTemplateForm->get('template')->getData();
                    $repo = $this->getDoctrine()->getRepository(Templates::class);
                    $template = $repo->findOneBy([
                        "id" => $templateId
                    ]);

                    if($template){
                        // replace value of report
                        $report->setTitle($template->getTitle());
                        $report->setTemplateId($template->getId());
                        $report->setStepsToReproduce($template->getStepsToReproduce());
                        $report->setImpact($template->getImpact());
                        $report->setMitigation($template->getMitigation());
                        $report->setRessources($template->getRessources());
                    }
                }
            } else {
                // Captcha failed
                $this->addFlash("error", $this->translator->trans("Sorry, something went wrong. Please, try again."));
            }
        }

        // get programs names
        $repo = $this->getDoctrine()->getRepository(Programs::class);
        $programs = $repo->findAll();
        $programsName = array();
        foreach($programs as $row){
            $programsName[$row->getName()] = $row->getId();
        }

        // create form create report
        $CreateReportForm = $this->createForm(CreateReportReportsType::class, $report, [
            "programsName" => $programsName
        ]);

        // handle requests
        $CreateReportForm->handleRequest($request);

        // Form sended
        if ($request->isMethod('post') && $CreateReportForm->isSubmitted()) {

            // Verify captcha
            $captchaToken = $request->get('g-recaptcha-response');
            if($this->captcha->verify($captchaToken)){
                /**
                 * Create report form sended
                 */
                if ($CreateReportForm->isValid()) {
                    // data valid
                    $report->setCreatorId($this->getUser()->getId());

                    // check that severity is between 0 and 10
                    if($report->getSeverity() > 10.0 or $report->getSeverity() < 0.0){
                        $CreateReportForm->get('severity')->addError(new FormError($this->translator->trans('The severity must be between 0.0 and 10.0')));
                    } else {
                        // save
                        $em = $this->getDoctrine()->getManager();
                        $em->persist($report);
                        $em->flush();

                        // success message
                        $this->addFlash("success", $this->translator->trans("The report was successfully created."));

                        // return
                        return $this->redirectToRoute('reports');
                    }
                }
            } else {
                // Captcha failed
                $this->addFlash("error", $this->translator->trans("Sorry, something went wrong. Please, try again."));
            }
        }

        return $this->render('reports/create.html.twig', [
            'captcha' => $this->captcha->enabled(),
            'publicKey' => $this->captcha->getPublicKey(),
            'CreateReportForm' => $CreateReportForm->createView(),
            'UseTemplateForm' => $UseTemplateForm->createView()
        ]);
    }

    /**
     * @Route("/delete/{id}", name="delete-report")
     */
    public function deleteReport(string $id): Response {
        // get report by id
        $repo = $this->getDoctrine()->getRepository(Reports::class);
        $report = $repo->findOneBy([
            "id" => $id
        ]);

        if($report){
            // delete report
            $em = $this->getDoctrine()->getManager();
            $em->remove($report);
            $em->flush();

            // success message
            $this->addFlash("success", $this->translator->trans("The report was successfully deleted."));

            // redirect
            return $this->redirectToRoute('reports');
        } else {
            $this->addFlash("error", $this->translator->trans("Sorry, the report cannot be found."));
            return $this->redirectToRoute('reports');
        }
    }

    /**
     * @Route("/edit/{id}", name="edit-report")
     */
    public function editReport(string $id, Request $request){
        // get report by id
        $repo = $this->getDoctrine()->getRepository(Reports::class);
        $report = $repo->findOneBy([
            "id" => $id
        ]);

        if($report){
            // get programs names
            $repo = $this->getDoctrine()->getRepository(Programs::class);
            $programs = $repo->findAll();
            $programsName = array();
            foreach($programs as $row){
                $programsName[$row->getName()] = $row->getId();
            }

            // create form edit report
            $EditReportForm = $this->createForm(CreateReportReportsType::class, $report, [
                "programsName" => $programsName
            ]);

            // handle requests
            $EditReportForm->handleRequest($request);

            // Form sended
            if ($request->isMethod('post')) {

                // Verify captcha
                $captchaToken = $request->get('g-recaptcha-response');
                if($this->captcha->verify($captchaToken)){
                    /**
                     * Create report form sended
                     */
                    if ($EditReportForm->isSubmitted() && $EditReportForm->isValid()) {
                        if($report->getSeverity() > 10.0 or $report->getSeverity() < 0.0){
                            $EditReportForm->get('severity')->addError(new FormError($this->translator->trans('The severity must be between 0.0 and 10.0')));
                        } else {
                            // save
                            $em = $this->getDoctrine()->getManager();
                            $em->persist($report);
                            $em->flush();

                            // success message
                            $this->addFlash("success", $this->translator->trans("The report was successfully edited."));

                            // return
                            return $this->redirectToRoute('reports');
                        }
                    }
                } else {
                    // Captcha failed
                    $this->addFlash("error", $this->translator->trans("Sorry, something went wrong. Please, try again."));
                }
            }

            return $this->render('reports/edit.html.twig', [
                'captcha' => $this->captcha->enabled(),
                'publicKey' => $this->captcha->getPublicKey(),
                'EditReportForm' => $EditReportForm->createView()
            ]);
        } else {
            $this->addFlash("error", $this->translator->trans("Sorry, the report cannot be found."));
            return $this->redirectToRoute('reports');
        }
    }

    /**
     * @Route("/show/{id}", name="show-report")
     */
    public function showReport(string $id){
        // get report by id
        $repo = $this->getDoctrine()->getRepository(Reports::class);
        $report = $repo->findOneBy([
            "id" => $id
        ]);

        if($report){
            // get program of report
            $repo = $this->getDoctrine()->getRepository(Programs::class);
            $program = $repo->findOneBy([
                "id" => $report->getProgramId()
            ]);

            return $this->render('reports/show.html.twig', [
                'captcha' => $this->captcha->enabled(),
                'publicKey' => $this->captcha->getPublicKey(),
                'report' => $report,
                'program' => $program
            ]);
        } else {
            $this->addFlash("error", $this->translator->trans("Sorry, the report cannot be found."));
            return $this->redirectToRoute('reports');
        }
    }

    /**
     * @Route("/generate-markdown", name="generate-markdown")
     */
    public function generateMarkdown(Request $request){
        // get data
        $title = $request->request->get('title');
        $date = $request->request->get('date');
        $severity = $request->request->get('severity');
        $endpoint = $request->request->get('endpoint');
        $program = $request->request->get('program');
        $repo = $this->getDoctrine()->getRepository(Programs::class);
        $program = $repo->findOneBy([
            "id" => $program
        ])->getName();
        $impact = $request->request->get('impact');
        $ressources = $request->request->get('ressources');
        $stepstoreproduce = $request->request->get('stepstoreproduce');
        $mitigation = $request->request->get('mitigation');

        $currentDate = new \DateTime();
        $currentDate = $currentDate->format('YmdHis');

        // create markdown
        $markdown = '# '.$title.'

---

**date**: '.$date.'

**severity (CVSS Scale)**: '.$severity.'

**endpoint**: '.$endpoint.'

**program**: '.$program.'

---

## Impact

'.$impact.'

---

## Steps to reproduce

'.$stepstoreproduce.'

---

## Ressources

'.$ressources.'

---

## Mitigation

'.$mitigation . '

---';
        // create file
        $newName = 'report-'.$currentDate;
        if (!is_dir($this->kernel->getProjectDir()."/public/markdown/")) {
            mkdir($this->kernel->getProjectDir()."/public/markdown/", 0775, true);
        }
        $path = $this->kernel->getProjectDir()."/public/markdown/".$newName.".md";

        // write markdown
        $myfile = fopen($path, "w") or die("Unable to open file!");
        fwrite($myfile, $markdown);
        fclose($myfile);

        // return path of file
        return new Response("/markdown/".$newName.".md", 200);
    }

    /**
     * @Route("/generate-pdf", name="generate-pdf")
     */
    public function generatePDF(Request $request){
        // get data
        $title = $request->request->get('title');
        $date = $request->request->get('date');
        $severity = $request->request->get('severity');
        $endpoint = $request->request->get('endpoint');
        $program = $request->request->get('program');
        $repo = $this->getDoctrine()->getRepository(Programs::class);
        $program = $repo->findOneBy([
            "id" => $program
        ])->getName();
        $impact = $request->request->get('impact');
        $ressources = $request->request->get('ressources');
        $stepstoreproduce = $request->request->get('stepstoreproduce');
        $mitigation = $request->request->get('mitigation');

        $currentDate = new \DateTime();
        $currentDate = $currentDate->format('YmdHis');

        // create path
        $newName = 'report-'.$currentDate;
        if (!is_dir($this->kernel->getProjectDir()."/public/pdf/")) {
            mkdir($this->kernel->getProjectDir()."/public/pdf/", 0775, true);
        }
        $path = $this->kernel->getProjectDir()."/public/pdf/".$newName.".pdf";

        // init
        $knpSnappyPdf = new Pdf('/usr/local/bin/wkhtmltopdf');

        // generate 
        $knpSnappyPdf->generateFromHtml(
            $this->renderView(
                'reports/pdf.html.twig',
                array(
                    'title'  => $title,
                    'date' => $date,
                    'severity' => $severity,
                    'endpoint' => $endpoint,
                    'program' => $program,
                    'impact' => $impact,
                    'ressources' => $ressources,
                    'stepstoreproduce' => $stepstoreproduce,
                    'mitigation' => $mitigation
                )
            ),
            $path
        );

        return new Response("/pdf/".$newName.".pdf",200);
    }
}
