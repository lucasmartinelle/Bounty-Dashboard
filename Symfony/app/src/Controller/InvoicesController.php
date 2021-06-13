<?php

namespace App\Controller;

use App\Entity\Reports;
use App\Entity\Platforms;
use App\Entity\Billing;
use App\Form\FilterInvoicesType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Controller\CaptchaController;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpKernel\KernelInterface;
use Knp\Snappy\Pdf;

/**
 * @Route("/invoices")
 */
class InvoicesController extends AbstractController
{
    private $captcha;
    private $translator;
    private $kernel;

    public function __construct(CaptchaController $captcha, TranslatorInterface $translator, KernelInterface $kernel)
    {
        $this->captcha = $captcha;
        $this->translator = $translator;
        $this->kernel = $kernel;
    }

    /**
     * @Route("", name="invoices")
     */
    public function index(Request $request): Response
    {
        // verify that invoice is actived for user
        if($this->getUser()->getIsActiveInvoices()){
            // get all report of user
            $repo = $this->getDoctrine()->getRepository(Reports::class);
            $reports = $repo->findBy([
                'creator_id' => $this->getUser()->getId()
            ]);

            $filterMonth = 'none';
            $filterPlatform = 'none';

            // get platforms names
            $repo = $this->getDoctrine()->getRepository(Platforms::class);
            $platforms = $repo->findAll();
            $platformsName = array();
            foreach($platforms as $row){
                $platformsName[$row->getName()] = $row->getId();
            }
            
            // init filter form
            $FiltersForm = $this->createForm(FilterInvoicesType::class, null, [
                "platformsName" => $platformsName
            ]);

            // handle Request
            $FiltersForm->handleRequest($request);

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
                        $filters = $this->formFilters($FiltersForm);

                        if($filters !== false){
                            // set reports as formFilters return
                            $reports = $filters;

                            // change filter informations
                            if(!empty(trim($FiltersForm->get('month')->getData()))){
                                $filterMonth = $FiltersForm->get('month')->getData();
                            }

                            if(!empty(trim($FiltersForm->get('platform')->getData()))){
                                $repo = $this->getDoctrine()->getRepository(Platforms::class);
                                $filterPlatform = $repo->findOneBy([
                                    "id" => $FiltersForm->get('platform')->getData()
                                ])->getName();
                            }
                        }
                    } elseif($FiltersForm->isSubmitted() && !$FiltersForm->isValid()){
                        // error message
                        $this->addFlash("error", $this->translator->trans("The form contains errors, please check the values entered."));
                    }
                } else {
                    $this->addFlash("error", $this->translator->trans("Sorry, something went wrong. Please, try again."));
                }
            }

            return $this->render('invoices/index.html.twig', [
                'captcha' => $this->captcha->enabled(),
                'publicKey' => $this->captcha->getPublicKey(),
                'reports' => $reports,
                'FiltersForm' => $FiltersForm->createView(),
                'filterPlatform' => $filterPlatform,
                'filterMonth' => $filterMonth
            ]);
        } else {
            return $this->redirectToRoute('reports');
        }
    }

    /**
     * Filters form
     * @param FilterInvoicesType : Form request from invoices
     * @return object|false
     */
    protected function formFilters(Form $FiltersForm){
        // get form fields
        $month = $FiltersForm->get('month')->getData();
        $platform = $FiltersForm->get('platform')->getData();

        if(empty(trim($month))){
            $month = null;
        }

        if(empty(trim($platform))){
            $platform = null;
        }

        // if there is at least one value filled
        if(!empty(trim($month)) || !empty(trim($platform))){
            
            // return find like in report repository
            $repo = $this->getDoctrine()->getRepository(Reports::class);
            return $repo->filtersInvoices($month, $platform, $this->getUser()->getId());
        }

        // else return false
        return false;
    }

    /**
     * Generate PDF invoice
     * @Route("generate", name="generate-invoice")
     * 
     */
    public function generateInvoice(Request $request){
        // Check if method is post
        if ($request->isMethod('post')) {
            // get month
            $month = $request->request->get('month');

            // get platform
            $platform = $request->request->get('platform');

            if($month == 'none' or $platform == 'none'){
                return new Response("month or platform not provided.",200);
            }
            
            // get report
            $requestReports = $request->request->get('reports');

            if(!$requestReports){
                return new Response("reports not provided",200);
            }

            // create object reports
            $reports = array();
            foreach($requestReports as $requestReport){
                $report = array();
                $report["title"] = $requestReport[0];
                $report["date"] = $requestReport[1];
                $report["gain"] = $requestReport[2];
                $report["identifier"] = $requestReport[3];
                array_push($reports, $report);
            }

            // get billing informations from user
            $repo = $this->getDoctrine()->getRepository(Billing::class);
            $billing = $repo->findOneBy([
                "user_id" => $this->getUser()->getId()
            ]);

            // get platform informations
            $repo = $this->getDoctrine()->getRepository(Platforms::class);
            $platform = $repo->findOneBy([
                "name" => $platform
            ]);

            // add one to nb Invoices.
            $this->getUser()->setNbInvoice($this->getUser()->getNbInvoice() + 1);

            $em = $this->getDoctrine()->getManager();
            $em->persist($this->getUser());
            $em->flush();

            // generate from twig template
            $currentDate = new \DateTime();
            $currentDate = $currentDate->format('YmdHis');
            // create path
            $newName = 'invoice2021'.$month.$this->getUser()->getNbInvoice().'-'.$currentDate;
            if (!is_dir($this->kernel->getProjectDir()."/public/pdf/")) {
                mkdir($this->kernel->getProjectDir()."/public/pdf/", 0775, true);
            }
            $path = $this->kernel->getProjectDir()."/public/pdf/".$newName.".pdf";

            // init
            $knpSnappyPdf = new Pdf('/usr/local/bin/wkhtmltopdf');

            $currency = ($this->getUser()->getLang() == "fr") ? '€' : '$';

            // generate 
            $knpSnappyPdf->generateFromHtml(
                $this->renderView(
                    'invoices/invoice.html.twig',
                    array(
                        'month'  => $month,
                        'invoiceNumber' => $this->getUser()->getNbInvoice(),
                        'reports' => $reports,
                        'billing' => $billing,
                        'platform' => $platform,
                        'currency' => $currency
                    )
                ),
                $path
            );

            return new Response("/pdf/".$newName.".pdf",200);
        }

        return false;
    }
}
