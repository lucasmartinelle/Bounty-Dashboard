<?php

namespace App\Controller;

use App\Entity\Reports;
use App\Entity\Programs;
use App\Entity\Platforms;
use App\Entity\Notes;
use App\Form\AddNoteProgramsType;
use App\Form\UpdateNoteProgramsType;
use App\Form\AddProgramProgramsType;
use App\Form\UpdateProgramProgramsType;
use App\Form\UpdateStatusProgramsType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Controller\CaptchaController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * @Route("/programs")
 */
class ProgramsController extends AbstractController
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
     * @Route("", name="programs")
     */
    public function index(Request $request): Response
    {
        // get informations for table
        $repo = $this->getDoctrine()->getRepository(Programs::class);
        $reportRepo = $this->getDoctrine()->getRepository(Reports::class);
        $watchall = $this->session->get('_watch_all', false);
        if($watchall){
            $programs = $repo->findAll();
        } else {
            $programs = $repo->findBy([
                "creator_id" => $this->getUser()->getId()
            ]);
        }

        $infos = array();

        foreach($programs as $program){
            $item = array();
            // get number of bugs
            $item["numberofbugs"] = $reportRepo->countBugs($program->getId());

            // get total gain
            $item["gaintotal"] = $reportRepo->sumGain($program->getId());

            // get scope number
            $item["scopenumber"] = count(explode("|",$program->getScope()))-1;

            // get platform name
            $item["platform"] = $repo->getPlatformName($program->getPlatformId());

            array_push($infos, $item);
        }

        // init object
        $program = new Programs();

        // get platforms name
        $repo = $this->getDoctrine()->getRepository(Platforms::class);
        $platforms = $repo->findAll();
        $platformsName = array();
        foreach($platforms as $row){
            $platformsName[$row->getName()] = $row->getId();
        }

        // get bugs by severity chart
        $repo = $this->getDoctrine()->getRepository(Reports::class);
        $severityChart = $repo->getBugBySeverity();

        // create form add platform
        $AddProgramForm = $this->createForm(AddProgramProgramsType::class, $program, [
            'platformsName' => $platformsName
        ]);

        // create form update status
        $UpdateStatusForm = $this->createForm(UpdateStatusProgramsType::class);

        // create form update program
        $UpdateProgramForm = $this->createForm(UpdateProgramProgramsType::class, null, [
            'platformsName' => $platformsName
        ]);

        // handle requests
        $AddProgramForm->handleRequest($request);
        $UpdateStatusForm->handleRequest($request);
        $UpdateProgramForm->handleRequest($request);

        // Form sended
        if ($request->isMethod('post')) {

            // Verify captcha
            $captchaToken = $request->get('g-recaptcha-response');
            if($this->captcha->verify($captchaToken)){

                /**
                 * Check for add program form
                 */
                if ($AddProgramForm->isSubmitted() && $AddProgramForm->isValid()) {
                    $program->setCreatorId($this->getUser()->getId());
                    $program->setPlatformId($AddProgramForm->get('platforms')->getData());
                    $program->setScope($AddProgramForm->get('scope_hidden')->getData());
                    $program->setTags($AddProgramForm->get('tags_hidden')->getData());
                    
                    // save
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($program);
                    $em->flush();

                    // success message
                    $this->addFlash("success", $this->translator->trans("The program was successfully created."));

                    return $this->redirectToRoute('programs');
                } elseif($AddProgramForm->isSubmitted() && !$AddProgramForm->isValid()) {
                    $this->addFlash("error", $this->translator->trans("The form contains errors, please check the values entered."));
                }

                /**
                 * Check for update status form
                 */
                if ($UpdateStatusForm->isSubmitted() && $UpdateStatusForm->isValid()) {
                    // get program by id
                    $repo = $this->getDoctrine()->getRepository(Programs::class);
                    $target = $repo->findOneBy([
                        "id" => $UpdateStatusForm->get('id')->getData()
                    ]);

                    if($target){
                        // update status
                        $target->setStatus($UpdateStatusForm->get('status')->getData());

                        // save
                        $em = $this->getDoctrine()->getManager();
                        $em->persist($target);
                        $em->flush();

                        // success message
                        $this->addFlash("success", $this->translator->trans("The status was successfully update."));
                    } else {
                        $this->addFlash("error", $this->translator->trans("The form contains errors, please check the values entered."));
                    }

                    return $this->redirectToRoute('programs');
                } elseif($UpdateStatusForm->isSubmitted() && !$UpdateStatusForm->isValid()) {
                    $this->addFlash("error", $this->translator->trans("The form contains errors, please check the values entered."));
                }

                /**
                 * Check for update program form
                 */
                if ($UpdateProgramForm->isSubmitted() && $UpdateProgramForm->isValid()) {
                    // get program by id
                    $repo = $this->getDoctrine()->getRepository(Programs::class);
                    $program = $repo->findOneBy([
                        "id" => $UpdateProgramForm->get('programId')->getData()
                    ]);

                    if(!$program){
                        $this->addFlash("error", $this->translator->trans("We couldn't find the program."));

                        return $this->redirectToRoute('programs'); 
                    }

                    // update platform data
                    if(!empty(trim($UpdateProgramForm->get('scope_hidden')->getData()))){
                        $program->setScope($UpdateProgramForm->get('scope_hidden')->getData());
                    }
                    if(!empty(trim($UpdateProgramForm->get('status')->getData()))){
                        $program->setStatus($UpdateProgramForm->get('status')->getData());
                    }
                    if(!empty(trim($UpdateProgramForm->get('tags_hidden')->getData()))){
                        $program->setTags($UpdateProgramForm->get('tags_hidden')->getData());
                    }
                    if(!empty(trim($UpdateProgramForm->get('platforms')->getData()))){
                        $program->setPlatform($UpdateProgramForm->get('platforms')->getData());
                    }

                    // save
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($program);
                    $em->flush();

                    // success message
                    $this->addFlash("success", $this->translator->trans("The program was successfully updated"));

                    // redirect
                    return $this->redirectToRoute('programs');

                } elseif($UpdatePlatformForm->isSubmitted() && !$UpdatePlatformForm->isValid()){   
                    $this->addFlash("error", $this->translator->trans("The form contains errors, please check the values entered."));
                }
            } else {
                // Captcha failed
                $this->addFlash("error", $this->translator->trans("Sorry, something went wrong. Please, try again."));
            }
        }

        return $this->render('programs/index.html.twig', [
            'captcha' => $this->captcha->enabled(),
            'publicKey' => $this->captcha->getPublicKey(),
            'AddProgramForm' => $AddProgramForm->createView(),
            'UpdateStatusForm' => $UpdateStatusForm->createView(),
            'UpdateProgramForm' => $UpdateProgramForm->createView(),
            'severityChart' => $severityChart,
            'programs' => $programs,
            'infos' => $infos
        ]);
    }

    /**
     * @Route("/delete/{id}", name="delete-program")
     */
    public function delete(string $id, Request $request){
        // get program by id
        $repo = $this->getDoctrine()->getRepository(Programs::class);
        $program = $repo->findOneBy([
            "id" => $id
        ]);

        if($program){
            // delete associated report
            $repo = $this->getDoctrine()->getRepository(Reports::class);
            $reports = $repo->findBy([
                "program_id" => $program->getId()
            ]);

            foreach($reports as $report){
                // delete report
                $em = $this->getDoctrine()->getManager();
                $em->remove($report);
                $em->flush();
            }

            // delete associated notes
            $repo = $this->getDoctrine()->getRepository(Notes::class);
            $notes = $repo->findBy([
                "program_id" => $program->getId()
            ]);

            foreach($notes as $note){
                // delete note
                $em = $this->getDoctrine()->getManager();
                $em->remove($note);
                $em->flush();
            }

            // delete program
            $em = $this->getDoctrine()->getManager();
            $em->remove($program);
            $em->flush();

            // success message
            $this->addFlash("success", $this->translator->trans("The program was successfully deleted."));

            // redirect
            return $this->redirectToRoute('programs');
        } else {
            $this->addFlash("error", $this->translator->trans("Sorry, the program cannot be found."));
            return $this->redirectToRoute('programs');
        }
    }

    /**
     * @Route("/notes/{id}", name="notes-program")
     */
    public function notes(string $id, Request $request){
        // init object
        $note = new Notes();
        $note->setProgramId($id);

        // get all notes
        $repo = $this->getDoctrine()->getRepository(Notes::class);
        $notes = $repo->findBy([
            "program_id" => $id
        ]);

        // get program name
        $repo = $this->getDoctrine()->getRepository(Programs::class);
        $program = $repo->findOneBy([
            "id" => $id
        ]);

        if($program){
            $program = $program->getName();

            // create form add notes
            $AddNoteForm = $this->createForm(AddNoteProgramsType::class, $note);

            // create form update notes
            $UpdateNoteForm = $this->createForm(UpdateNoteProgramsType::class);

            // handle requests
            $AddNoteForm->handleRequest($request);
            $UpdateNoteForm->handleRequest($request);

            // Form sended
            if ($request->isMethod('post')) {

                // Verify captcha
                $captchaToken = $request->get('g-recaptcha-response');
                if($this->captcha->verify($captchaToken)){

                    /**
                     * Check for add note form
                     */
                    if ($AddNoteForm->isSubmitted() && $AddNoteForm->isValid()) {
                        // save
                        $em = $this->getDoctrine()->getManager();
                        $em->persist($note);
                        $em->flush();

                        // success message
                        $this->addFlash("success", $this->translator->trans("The note was successfully created."));

                        return $this->redirectToRoute('notes-program', ['id' => $id]);
                    } elseif($AddNoteForm->isSubmitted() && !$AddNoteForm->isValid()) {
                        $this->addFlash("error", $this->translator->trans("The form contains errors, please check the values entered."));
                    }

                    /**
                     * Check for update note form
                     */
                    if ($UpdateNoteForm->isSubmitted() && $UpdateNoteForm->isValid()) {
                        $idNote = $UpdateNoteForm->get('id')->getData();

                        $repo = $this->getDoctrine()->getRepository(Notes::class);
                        $noteToUpdate = $repo->findOneBy([
                            "id" => $idNote
                        ]);

                        if($noteToUpdate){
                            $title = $UpdateNoteForm->get('title')->getData();
                            if($title){
                                $noteToUpdate->setTitle($title);
                            }
                            $text = $UpdateNoteForm->get('text')->getData();
                            if($text){
                                $noteToUpdate->setText($text);
                            }

                            // save
                            $em = $this->getDoctrine()->getManager();
                            $em->persist($noteToUpdate);
                            $em->flush();

                            // success message
                            $this->addFlash("success", $this->translator->trans("The note was successfully updated."));
                        } else {
                            $this->addFlash("error", $this->translator->trans("The form contains errors, please check the values entered."));
                        }

                        return $this->redirectToRoute('notes-program', ['id' => $id]);
                    } elseif($UpdateNoteForm->isSubmitted() && !$UpdateNoteForm->isValid()) {
                        $this->addFlash("error", $this->translator->trans("The form contains errors, please check the values entered."));
                    }
                } else {
                    // Captcha failed
                    $this->addFlash("error", $this->translator->trans("Sorry, something went wrong. Please, try again."));
                }
            }

            return $this->render('programs/notes.html.twig', [
                'captcha' => $this->captcha->enabled(),
                'publicKey' => $this->captcha->getPublicKey(),
                'AddNoteForm' => $AddNoteForm->createView(),
                'UpdateNoteForm' => $UpdateNoteForm->createView(),
                'name' => $program,
                'notes' => $notes
            ]);
        } else {
            $this->addFlash("error", $this->translator->trans("The program requested cannot be found."));

            return $this->redirectToRoute('programs', ['id' => $id]);
        }
    }

    /**
     * @Route("/delete-notes/{id}", name="delete-note")
     */
    public function deleteNote(string $id, Request $request){
        return $this->render('programs/notes.html.twig', [
            'captcha' => $this->captcha->enabled(),
            'publicKey' => $this->captcha->getPublicKey()
        ]);
    }

    /**
     * @Route("/retrieve-data/{param}", name="retrieve-data")
     */
    public function retrieveData(string $param, Request $request){
        // get data
        $value = $request->get('term');
        $repo = $this->getDoctrine()->getRepository(Programs::class);
        $data = $repo->findLike($param, $value);
        
        // return data
        return new Response($data, 200);
    }
}
