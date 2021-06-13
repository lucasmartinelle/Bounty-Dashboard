<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class MainController extends AbstractController
{
    
    private $session;
    private $translator;

    public function __construct(SessionInterface $session, TranslatorInterface $translator)
    {
        $this->session = $session;
        $this->translator = $translator;
    }

    /**
     * @Route("/change_locale/{locale}", name="change_locale")
     */
    public function index($locale, Request $request)
    {
        $redirection = $request->headers->get('referer');

        if($this->session->has('REDIRECT_FROM') == "authentificationSuccess"){
            $redirection = 'dashboard';
            $this->session->remove('REDIRECT_FROM');
        }

        if($locale == 'en' || $locale == 'fr'){
            $request->getSession()->set('_locale', $locale);

            // On stocke la langue dans la base de données
            $email = $request->getSession()->get('_security.last_username');

            $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' => $email]);

            if (!$user) {
                return $this->redirect($redirection);
            }
            
            $user->setLang($locale);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
        }
        
        return $this->redirect($redirection);
    }

    /**
     * @Route("/change-watch-status", name="change-watch-status")
     */
    public function changeWatchStatus(Request $request){
        if(!$this->getUser()){
            return $this->redirect($request->headers->get('referer'));
        }

        $currentWatchStatus = $request->getSession()->get('_watch_all', false);

        if($currentWatchStatus){
            $request->getSession()->set('_watch_all', false);
        } else {
            $request->getSession()->set('_watch_all', true);
        }

        return $this->redirect($request->headers->get('referer'));
    }
}
