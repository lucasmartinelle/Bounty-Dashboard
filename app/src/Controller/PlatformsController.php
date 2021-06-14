<?php

namespace App\Controller;

use App\Form\AddProgramProgramsType;
use App\Form\AddPlatformPlatformsType;
use App\Form\AddReportReportsType;
use App\Form\UpdatePlatformPlatformsType;
use App\Form\ImportDataPlatformsType;
use App\Form\FilterPlatformsType;
use App\Entity\Platforms;
use App\Entity\Programs;
use App\Entity\Reports;
use App\Entity\Notes;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Controller\CaptchaController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\Form;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validation;

/**
 * @Route("/platforms")
 */
class PlatformsController extends AbstractController
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
     * @Route("", name="platforms")
     */
    public function index(Request $request, SluggerInterface $slugger): Response
    {
        // get all current platforms
        $repo = $this->getDoctrine()->getRepository(Platforms::class);
        $platforms = $repo->findAll();

        // get bugs by severity chart
        $repo = $this->getDoctrine()->getRepository(Reports::class);
        $severityChart = $repo->getBugBySeverity();

        // get earning per month chart
        $repo = $this->getDoctrine()->getRepository(Reports::class);
        $earningPerMonthChart = $repo->earningPerMonth();
        $earningPerMonthChartActualYear = 'all';

        // init object
        $platform = new Platforms();

        // create form add platform
        $AddPlatformForm = $this->createForm(AddPlatformPlatformsType::class, $platform);

        // create form update platform
        $UpdatePlatformForm = $this->createForm(UpdatePlatformPlatformsType::class);

        // create form import data
        $ImportDataForm = $this->createForm(ImportDataPlatformsType::class);

        // create form filter earning per month
        $filterEarningPerMonthForm = $this->createForm(FilterPlatformsType::class);

        // handle requests
        $AddPlatformForm->handleRequest($request);
        $UpdatePlatformForm->handleRequest($request);
        $ImportDataForm->handleRequest($request);
        $filterEarningPerMonthForm->handleRequest($request);

        // Form sended
        if ($request->isMethod('post')) {

            // Verify captcha
            $captchaToken = $request->get('g-recaptcha-response');
            if($this->captcha->verify($captchaToken)){

                /**
                 * Check for add platform form
                 */
                if ($AddPlatformForm->isSubmitted() && $AddPlatformForm->isValid()) {
                    $this->addPlatformForm($AddPlatformForm, $platform);

                    // redirect
                    return $this->redirectToRoute('platforms');
                } elseif($AddPlatformForm->isSubmitted() && !$AddPlatformForm->isValid()){   
                    $this->addFlash("error", $this->translator->trans("The form contains errors, please check the values entered."));
                }

                /**
                 * Check for update platform form
                 */
                if ($UpdatePlatformForm->isSubmitted() && $UpdatePlatformForm->isValid()) {
                    $this->updatePlatformForm($updatePlatformForm, $platform);

                    // redirect
                    return $this->redirectToRoute('platforms');

                } elseif($UpdatePlatformForm->isSubmitted() && !$UpdatePlatformForm->isValid()){   
                    $this->addFlash("error", $this->translator->trans("The form contains errors, please check the values entered."));
                }

                /**
                 * Check for import platform form
                 */
                if ($ImportDataForm->isSubmitted() && $ImportDataForm->isValid()) {
                    $this->importDataForm($ImportDataForm, $slugger);

                    // redirect
                    return $this->redirectToRoute('platforms');
                } elseif($ImportDataForm->isSubmitted() && !$ImportDataForm->isValid()){
                    $this->addFlash("error", $this->translator->trans("The form contains errors, please check the values entered."));
                    return $this->redirectToRoute('platforms');
                }

                /**
                 * Check for filter earning per month form
                 */
                if($filterEarningPerMonthForm->isSubmitted() && $filterEarningPerMonthForm->isValid()){
                    // get earning per month for a specific year
                    $repo = $this->getDoctrine()->getRepository(Reports::class);
                    $earningPerMonthChart = $repo->earningPerMonth($filterEarningPerMonthForm->get('year')->getData());
                    $earningPerMonthChartActualYear = $filterEarningPerMonthForm->get('year')->getData();
                }
            } else {
                // Captcha failed
                $this->addFlash("error", $this->translator->trans("Sorry, something went wrong. Please, try again."));
                return $this->redirectToRoute('platforms');
            }
        }

        return $this->render('platforms/index.html.twig', [
            'captcha' => $this->captcha->enabled(),
            'publicKey' => $this->captcha->getPublicKey(),
            'AddPlatformForm' => $AddPlatformForm->createView(),
            'UpdatePlatformForm' => $UpdatePlatformForm->createView(),
            'ImportDataForm' => $ImportDataForm->createView(),
            'filterEarningPerMonthForm' => $filterEarningPerMonthForm->createView(),
            'severityChart' => $severityChart,
            'earningPerMonthChart' => $earningPerMonthChart,
            'earningPerMonthChartActualYear' => $earningPerMonthChartActualYear,
            'platforms' => $platforms
        ]);
    }

    /**
     * Add platform form
     * @param AddPlatformPlatformsType : Form request from platforms
     * @param Platforms : Platforms entity
     * @return true|false
     */
    protected function addPlatformForm(Form $addPlatformForm, Platforms $platform){
        // add user id and format date
        $platform->setCreatorId($this->getUser()->getId());

        // save
        $em = $this->getDoctrine()->getManager();
        $em->persist($platform);
        $em->flush();

        // success message
        $this->addFlash("success", $this->translator->trans("The platform was successfully created"));

        return true;
    }

    /**
     * Add platform form
     * @param updatePlatformPlatformsType : Form request from platforms
     * @param Platforms : Platforms entity
     * @return true|false
     */
    protected function updatePlatformForm(Form $updatePlatformForm, Platforms $platform){
        // get platform by id
        $repo = $this->getDoctrine()->getRepository(Platforms::class);
        $platform = $repo->findOneBy([
            "id" => $UpdatePlatformForm->get('platformId')->getData()
        ]);

        if(!$platform){
            $this->addFlash("error", $this->translator->trans("We couldn't find the platform."));

            return false;
        }

        // update platform data
        if(!empty(trim($UpdatePlatformForm->get('client')->getData()))){
            $platform->setClient($UpdatePlatformForm->get('client')->getData());
        }
        if(!empty(trim($UpdatePlatformForm->get('btw')->getData()))){
            $platform->setBtw($UpdatePlatformForm->get('btw')->getData());
        }
        if(!empty(trim($UpdatePlatformForm->get('address')->getData()))){
            $platform->setAddress($UpdatePlatformForm->get('address')->getData());
        }
        if(!empty(trim($UpdatePlatformForm->get('email')->getData()))){
            $platform->setEmail($UpdatePlatformForm->get('email')->getData());
        }
        if(!empty(trim($UpdatePlatformForm->get('date')->getData()))){
            $platform->setDate($UpdatePlatformForm->get('date')->getData());
        }

        // save
        $em = $this->getDoctrine()->getManager();
        $em->persist($platform);
        $em->flush();

        // success message
        $this->addFlash("success", $this->translator->trans("The platform was successfully updated"));

        return true;
    }

    /**
     * import data form
     * @param ImportDataPlatformsType : Form request from platforms
     * @return true|false
     */
    protected function importDataForm(Form $ImportDataForm, SluggerInterface $slugger){
        // get file
        $dataFile = $ImportDataForm->get('file')->getData();
        $originalFilename = pathinfo($dataFile->getClientOriginalName(), PATHINFO_FILENAME);

        // this is needed to safely include the file name as part of the URL
        $safeFilename = $slugger->slug($originalFilename);
        $newFilename = $safeFilename.'-'.uniqid().'.'.$dataFile->guessExtension();

        // Move the file to the directory where brochures are stored
        try {
            $dataFile->move(
                $this->getParameter('kernel.project_dir').'/public/dist/import/',
                $newFilename
            );
        } catch (FileException $e) {
            $this->addFlash("error", $this->translator->trans("Sorry, something went wrong. Please, try again."));
            
            return false;
        }

        $fullpath = $this->getParameter('kernel.project_dir').'/public/dist/import/'.$newFilename;
        $errors = [];
        // headers list
        $headersRead = false;
        // read file
        if (($fp = fopen($fullpath, "r")) !== FALSE) {
            // Read the file
            while (($row = fgetcsv($fp, 1000, $this->detectDelimiter($fullpath))) !== FALSE) {
                // skip headers
                if($headersRead){
                    // save imported programs with errors
                    $programWithErrors = [];
                    // check if a new program is provided
                    if(!empty(trim($row[12]))){
                        // check if it ain't already exist
                        $repo = $this->getDoctrine()->getRepository(Programs::class);
                        $program = $repo->findOneBy([
                            "name" => htmlspecialchars($row[12], ENT_QUOTES)
                        ]);

                        // if exist, add a error message.
                        if($program){
                            array_push($errors, $this->translator->trans("The program ").htmlspecialchars($row[12], ENT_QUOTES).$this->translator->trans(" already exists, if a report is provided with it, it will automatically be linked to this program"));
                        // else validate data
                        } else {
                            $repo = $this->getDoctrine()->getRepository(Platforms::class);
                            $platforms = $repo->findAll();
                            $platformsName = array();
                            foreach($platforms as $p){
                                array_push($platformsName, $p->getId());
                            }

                            $input = [
                                'name' => htmlspecialchars($row[12], ENT_QUOTES),
                                'scope' => htmlspecialchars($row[13], ENT_QUOTES),
                                'status' => htmlspecialchars($row[15], ENT_QUOTES),
                                'platform' => $ImportDataForm->get('platform')->getData()
                            ];

                            $groups = new Assert\GroupSequence(['Default', 'custom']);

                            $constraint  = new Assert\Collection([
                                'name' => new Assert\Sequentially([
                                    new Assert\NotBlank(),
                                    new Assert\Length(['max' => 255]),
                                ]),
                                'scope' => new Assert\Sequentially([
                                    new Assert\NotBlank(),
                                    new Assert\Length(['max' => 255])
                                ]),
                                'status' => new Assert\Choice(['Open', 'Close']),
                                'platform' => new Assert\Choice($platformsName)
                            ]);

                            $validator = Validation::createValidator();
                            $violations = $validator->validate($input, $constraint, $groups);

                            // if validation failed, error message
                            if(!(0 === count($violations))){
                                array_push($errors, $this->translator->trans("The program ").htmlspecialchars($row[12], ENT_QUOTES).$this->translator->trans(" contains some errors. We could not save data."));
                                array_push($programWithErrors, htmlspecialchars($row[12], ENT_QUOTES));
                            // else save program
                            } else {
                                $program = new Programs();

                                $program->setCreatorId($this->getUser()->getId());
                                $program->setName(htmlspecialchars($row[12], ENT_QUOTES));
                                $program->setScope(htmlspecialchars($row[13], ENT_QUOTES));
                                if(!empty(trim(htmlspecialchars($row[14], ENT_QUOTES)))){
                                    $program->setDate(new \Datetime(str_replace("/", "-", htmlspecialchars($row[14], ENT_QUOTES))));
                                }
                                $program->setStatus(htmlspecialchars($row[15], ENT_QUOTES));
                                if(!empty(trim(htmlspecialchars($row[16], ENT_QUOTES)))){
                                    $program->setTags(htmlspecialchars($row[16], ENT_QUOTES));
                                }
                                $program->setPlatformId($ImportDataForm->get('platform')->getData());

                                $em = $this->getDoctrine()->getManager();
                                $em->persist($program);
                                $em->flush();
                            }
                        }
                    }

                    // check if a report is provided
                    if(!empty(trim($row[0]))){
                        // check if ain't already exist
                        $repo = $this->getDoctrine()->getRepository(Reports::class);
                        $report = $repo->findOneBy([
                            "identifiant" => htmlspecialchars($row[4], ENT_QUOTES)
                        ]);

                        if($report){
                            array_push($errors, $this->translator->trans("The report with the identifier ").htmlspecialchars($row[4], ENT_QUOTES).$this->translator->trans(" already exists. It could not be saved."));
                        // else check data
                        } else {
                            // check program is not in programsWithErrors
                            if(!in_array(htmlspecialchars($row[4], ENT_QUOTES), $programWithErrors)){
                                $repo = $this->getDoctrine()->getRepository(Programs::class);
                                $programs = $repo->findAll();
                                $programsName = array();
                                foreach($programs as $p){
                                    array_push($programsName, $p->getName());
                                }

                                $input = [
                                    'title' => htmlspecialchars($row[0], ENT_QUOTES),
                                    'endpoint' => htmlspecialchars($row[3], ENT_QUOTES),
                                    'identifiant' => htmlspecialchars($row[4], ENT_QUOTES),
                                    'status' => htmlspecialchars($row[5], ENT_QUOTES),
                                    'program' => htmlspecialchars($row[7], ENT_QUOTES),
                                ];

                                $groups = new Assert\GroupSequence(['Default', 'custom']);

                                $constraint  = new Assert\Collection([
                                    'title' => new Assert\Sequentially([
                                        new Assert\NotBlank(),
                                        new Assert\Length(['max' => 200]),
                                    ]),
                                    'endpoint' => new Assert\Sequentially([
                                        new Assert\Length(['max' => 255])
                                    ]),
                                    'identifiant' => new Assert\Sequentially([
                                        new Assert\NotBlank(),
                                        new Assert\Length(['max' => 200]),
                                    ]),
                                    'status' => new Assert\Choice(['Informative', 'New', 'Accepted', 'Resolved', 'NA', 'OOS']),
                                    'program' => new Assert\Choice($programsName)
                                ]);
                            
                                
                                $validator = Validation::createValidator();
                                $violations = $validator->validate($input, $constraint, $groups);

                                // if validation failed, error message
                                if(!(0 === count($violations))){
                                    dd($violations);
                                    array_push($errors, $this->translator->trans("The report with the identifier ").htmlspecialchars($row[4], ENT_QUOTES).$this->translator->trans(" contains some errors. We could not save data."));
                                // else save the report
                                } else {
                                    $repo = $this->getDoctrine()->getRepository(Programs::class);
                                    $programId = $repo->findOneBy([
                                        "name" => htmlspecialchars($row[7], ENT_QUOTES)
                                    ])->getId();

                                    $report = new Reports();

                                    $report->setCreatorId($this->getUser()->getId());
                                    $report->setTitle(htmlspecialchars($row[0], ENT_QUOTES));
                                    if(!empty(trim(htmlspecialchars($row[1], ENT_QUOTES)))){
                                        $severity = (float) htmlspecialchars($row[1], ENT_QUOTES);
                                        if($severity >= 0 and $severity <= 10){
                                            $report->setSeverity($severity);
                                        }
                                    }
                                    if(!empty(trim(htmlspecialchars($row[2], ENT_QUOTES)))){
                                        $report->setDate(new \Datetime(str_replace("/", "-", htmlspecialchars($row[2], ENT_QUOTES))));
                                    }
                                    if(!empty(trim(htmlspecialchars($row[3], ENT_QUOTES)))){
                                        $report->setEndpoint(htmlspecialchars($row[3], ENT_QUOTES));
                                    }
                                    $report->setIdentifiant(htmlspecialchars($row[4], ENT_QUOTES));
                                    $report->setStatus(htmlspecialchars($row[5], ENT_QUOTES));
                                    if(!empty(trim(htmlspecialchars($row[6], ENT_QUOTES)))){
                                        $gain = (int) htmlspecialchars($row[6], ENT_QUOTES);
                                        $report->setGain($gain);
                                    }
                                    $report->setProgramId($programId);
                                    if(!empty(trim(htmlspecialchars($row[8], ENT_QUOTES)))){
                                        $report->setStepsToReproduce(htmlspecialchars($row[8], ENT_QUOTES));
                                    }
                                    if(!empty(trim(htmlspecialchars($row[9], ENT_QUOTES)))){
                                        $report->setImpact(htmlspecialchars($row[9], ENT_QUOTES));
                                    }
                                    if(!empty(trim(htmlspecialchars($row[10], ENT_QUOTES)))){
                                        $report->setMitigation(htmlspecialchars($row[10], ENT_QUOTES));
                                    }
                                    if(!empty(trim(htmlspecialchars($row[11], ENT_QUOTES)))){
                                        $report->setRessources(htmlspecialchars($row[11], ENT_QUOTES));
                                    }

                                    $em = $this->getDoctrine()->getManager();
                                    $em->persist($report);
                                    $em->flush();
                                }
                            } else {
                                array_push($errors, $this->translator->trans("The report with the identifier ").htmlspecialchars($row[4], ENT_QUOTES).$this->translator->trans(" could not be saved."));
                            }
                        }
                    }
                } else {
                    $headersRead = true;
                }
                
            }
            fclose($fp);
        }

        // delete imported file
        unlink($fullpath);

        // check if there is some errors
        if(count($errors) === 0){
            // if there is not, success message and return true
            $this->addFlash("success", $this->translator->trans("The import is a success."));
            
            return true;
        } else {
            // else error message and return false
            $this->addFlash("error", $errors);
            
            return false;
        }
    }

    /**
     * @Route("/delete/{id}", name="delete-platform")
     */
    public function delete(string $id, Request $request): Response
    {
        // get platform by id
        $repo = $this->getDoctrine()->getRepository(Platforms::class);
        $platform = $repo->findOneBy([
            "id" => $id
        ]);

        if($platform){
            // get all programs of the platform
            $repo = $this->getDoctrine()->getRepository(Programs::class);
            $programs = $repo->findBy([
                "platform_id" => $platform->getId()
            ]);

            if($programs){
                // fetch programs
                foreach($programs as $program){
                    // get all reports of program
                    $repo = $this->getDoctrine()->getRepository(Reports::class);
                    $reports = $repo->findBy([
                        "program_id" => $program->getId()
                    ]);

                    if($reports){
                        // fetch reports
                        foreach($reports as $report){
                            // delete report
                            $em = $this->getDoctrine()->getManager();
                            $em->remove($report);
                            $em->flush();
                        }
                    }

                    // get all notes of program
                    $repo = $this->getDoctrine()->getRepository(Notes::class);
                    $notes = $repo->findBy([
                        "program_id" => $program->getId()
                    ]);

                    if($notes){
                        // fetch notes
                        foreach($notes as $note){
                            // delete report
                            $em = $this->getDoctrine()->getManager();
                            $em->remove($note);
                            $em->flush();
                        }
                    }

                    // delete program
                    $em = $this->getDoctrine()->getManager();
                    $em->remove($program);
                    $em->flush();
                }
            }


            // delete platform
            $em = $this->getDoctrine()->getManager();
            $em->remove($platform);
            $em->flush();

            // success message
            $this->addFlash("success", $this->translator->trans("The platform was successfully deleted."));

            // redirect
            return $this->redirectToRoute('platforms');
        } else {
            $this->addFlash("error", $this->translator->trans("Sorry, the platform cannot be found."));
            return $this->redirectToRoute('platforms');
        }
    }

    /**
    * @param string $csvFile Path to the CSV file
    * @return string Delimiter
    */
    private function detectDelimiter($csvFile)
    {
        $delimiters = [";" => 0, "," => 0, "\t" => 0, "|" => 0];

        $handle = fopen($csvFile, "r");
        $firstLine = fgets($handle);
        fclose($handle); 
        foreach ($delimiters as $delimiter => &$count) {
            $count = count(str_getcsv($firstLine, $delimiter));
        }

        return array_search(max($delimiters), $delimiters);
    }
}
