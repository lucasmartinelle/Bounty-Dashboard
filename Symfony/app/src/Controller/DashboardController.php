<?php

namespace App\Controller;

use App\Entity\Platforms;
use App\Entity\Reports;
use App\Entity\Programs;
use App\Form\FilterBugsFoundPerMonthDashboardType;
use App\Form\FilterBugsFoundBySeverityDashboardType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Controller\CaptchaController;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * @Route("/dashboard")
 */
class DashboardController extends AbstractController
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
     * @Route("", name="dashboard")
     */
    public function index(Request $request): Response
    {
        $repo = $this->getDoctrine()->getRepository(Reports::class);

        // get bugsOpened
        $bugsOpened = $repo->bugsOpened();

        // get bugsAcceptedAndFixed
        $bugsAcceptedAndFixed = $repo->bugsAcceptedAndFixed();
        
        // get totalEarnings
        $totalEarnings = $repo->totalEarnings();

        // get criticalsBugsFounds
        $criticalsBugsFounds = $repo->criticalsBugsFounds();

        // set currency
        $currency;
        if($this->getUser()->getLang() == 'fr'){
            $currency = '€';
        } else {
            $currency = '$';
        }

        // get bugs founds per month
        $bugsFoundsPerMonth = $repo->bugsFoundsPerMonth();
        $bugsFoundsPerMonthFilterPlatform = $this->translator->trans("all");
        $bugsFoundsPerMonthFilterYear = $this->translator->trans("all");

        // get bugs founds by severity
        $bugsFoundsBySeverity = $repo->bugsFoundsBySeverity();
        $bugsFoundsBySeverityFilterPlatform = $this->translator->trans("all");
        $bugsFoundsBySeverityFilterProgram = $this->translator->trans("all");

        
        $bugsFoundsByPlatforms = $repo->bugsFoundsByPlatforms();
        
        // get platforms names
        $repo = $this->getDoctrine()->getRepository(Platforms::class);
        $platforms = $repo->findAll();
        $platformsName = array();
        foreach($platforms as $row){
            $platformsName[$row->getName()] = $row->getId();
        }
        
        // get programs names
        $repo = $this->getDoctrine()->getRepository(Programs::class);
        $programs = $repo->findAll();
        $programsName = array();
        foreach($programs as $row){
            $programsName[$row->getName()] = $row->getId();
        }

        // init forms
        $FiltersBugsFoundPerMonthForm = $this->createForm(FilterBugsFoundPerMonthDashboardType::class, null, [
            "platformsName" => $platformsName
        ]);

        $FiltersBugsFoundBySeverityForm = $this->createForm(FilterBugsFoundBySeverityDashboardType::class, null, [
            "programsName" => $programsName,
            "platformsName" => $platformsName
        ]);

        // handle request
        $FiltersBugsFoundPerMonthForm->handleRequest($request);
        $FiltersBugsFoundBySeverityForm->handleRequest($request);

        // Form sended
        if ($request->isMethod('post')) {

            // Verify captcha
            $captchaToken = $request->get('g-recaptcha-response');
            if($this->captcha->verify($captchaToken)){
                /**
                 * Filters Bugs Found Per Month form sended
                 */
                if ($FiltersBugsFoundPerMonthForm->isSubmitted() && $FiltersBugsFoundPerMonthForm->isValid()) {
                    // Verify if year is provided or not
                    if($FiltersBugsFoundPerMonthForm->get('year')->getData()){
                        $bugsFoundsPerMonthFilterYear = $FiltersBugsFoundPerMonthForm->get('year')->getData();
                    } else {
                        $bugsFoundsPerMonthFilterYear = null;
                    }
                    // Verify platform is provided or not
                    if($FiltersBugsFoundPerMonthForm->get('platform')->getData()){
                        $bugsFoundsPerMonthFilterPlatform = $FiltersBugsFoundPerMonthForm->get('platform')->getData();
                    } else {
                        $bugsFoundsPerMonthFilterPlatform = null;
                    }

                    // get bugsFoundsPerMonth
                    $repo = $this->getDoctrine()->getRepository(Reports::class);
                    $bugsFoundsPerMonth = $repo->bugsFoundsPerMonth($bugsFoundsPerMonthFilterPlatform, $bugsFoundsPerMonthFilterYear);

                    // reset bugsFoundsPerMonthFilterPlatform
                    if($FiltersBugsFoundPerMonthForm->get('platform')->getData()){
                        $repo = $this->getDoctrine()->getRepository(Platforms::class);
                        $bugsFoundsPerMonthFilterPlatform = $repo->findOneBy(["id" => $FiltersBugsFoundPerMonthForm->get('platform')->getData()])->getName();
                    } else {
                        $bugsFoundsPerMonthFilterPlatform = $this->translator->trans("all");
                    }

                    // reset bugsFoundsPerMonthFilterYear
                    if(!$FiltersBugsFoundPerMonthForm->get('year')->getData()){
                        $bugsFoundsPerMonthFilterYear = $this->translator->trans("all");
                    }
                } elseif($FiltersBugsFoundPerMonthForm->isSubmitted() && !$FiltersBugsFoundPerMonthForm->isValid()){
                    // error message
                    $this->addFlash("error", $this->translator->trans("The form contains errors, please check the values entered."));
                }

                /**
                 * Filters Bugs Found Per Month form sended
                 */
                if ($FiltersBugsFoundBySeverityForm->isSubmitted() && $FiltersBugsFoundBySeverityForm->isValid()) {
                    // Verify if program is provided or not
                    if($FiltersBugsFoundBySeverityForm->get('program')->getData()){
                        $bugsFoundsBySeverityFilterProgram = $FiltersBugsFoundBySeverityForm->get('program')->getData();
                    } else {
                        $bugsFoundsBySeverityFilterProgram = null;
                    }
                    // Verify platform is provided or not
                    if($FiltersBugsFoundBySeverityForm->get('platform')->getData()){
                        $bugsFoundsBySeverityFilterPlatform = $FiltersBugsFoundBySeverityForm->get('platform')->getData();
                    } else {
                        $bugsFoundsBySeverityFilterPlatform = null;
                    }

                    // get bugsFoundsBySeverity
                    $repo = $this->getDoctrine()->getRepository(Reports::class);
                    $bugsFoundsByPlatforms = $repo->bugsFoundsBySeverity($bugsFoundsBySeverityFilterPlatform, $bugsFoundsBySeverityFilterProgram);

                    // reset bugsFoundsBySeverityFilterPlatform
                    if($FiltersBugsFoundBySeverityForm->get('platform')->getData()){
                        $repo = $this->getDoctrine()->getRepository(Platforms::class);
                        $bugsFoundsBySeverityFilterPlatform = $repo->findOneBy(["id" => $FiltersBugsFoundBySeverityForm->get('platform')->getData()])->getName();
                    } else {
                        $bugsFoundsBySeverityFilterPlatform = $this->translator->trans("all");
                    }

                    // reset bugsFoundsBySeverityFilterProgram
                    if($FiltersBugsFoundBySeverityForm->get('program')->getData()){
                        $repo = $this->getDoctrine()->getRepository(Programs::class);
                        $bugsFoundsBySeverityFilterProgram = $repo->findOneBy(["id" => $FiltersBugsFoundBySeverityForm->get('program')->getData()])->getName();
                    } else {
                        $bugsFoundsBySeverityFilterProgram = $this->translator->trans("all");
                    }
                } elseif($FiltersBugsFoundBySeverityForm->isSubmitted() && !$FiltersBugsFoundBySeverityForm->isValid()){
                    // error message
                    $this->addFlash("error", $this->translator->trans("The form contains errors, please check the values entered."));
                }
            } else {
                $this->addFlash("error", $this->translator->trans("Sorry, something went wrong. Please, try again."));
            }
        }

        return $this->render('dashboard/index.html.twig', [
            'captcha' => $this->captcha->enabled(),
            'publicKey' => $this->captcha->getPublicKey(),
            'bugsOpened' => $bugsOpened,
            'bugsAcceptedAndFixed' => $bugsAcceptedAndFixed,
            'totalEarnings' => $totalEarnings,
            'criticalsBugsFounds' => $criticalsBugsFounds,
            'bugsFoundsPerMonth' => $bugsFoundsPerMonth,
            'bugsFoundsBySeverity' => $bugsFoundsBySeverity,
            'bugsFoundsByPlatforms' => $bugsFoundsByPlatforms,
            'bugsFoundsPerMonthFilterPlatform' => $bugsFoundsPerMonthFilterPlatform,
            'bugsFoundsPerMonthFilterYear' => $bugsFoundsPerMonthFilterYear,
            'bugsFoundsBySeverityFilterPlatform' => $bugsFoundsBySeverityFilterPlatform,
            'bugsFoundsBySeverityFilterProgram' => $bugsFoundsBySeverityFilterProgram,
            'FiltersBugsFoundPerMonthForm' => $FiltersBugsFoundPerMonthForm->createView(),
            'FiltersBugsFoundBySeverityForm' => $FiltersBugsFoundBySeverityForm->createView(),
            'currency' => $currency
        ]);
    }
}
