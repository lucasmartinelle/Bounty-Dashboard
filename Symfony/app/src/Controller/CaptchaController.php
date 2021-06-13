<?php

namespace App\Controller;

use App\Entity\Captcha;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;

class CaptchaController extends AbstractController
{
    private $privateKey;
    private $publicKey;
    private $session;
    private $translator;

    public function __construct(SessionInterface $session, TranslatorInterface $translator)
    {
        $this->session = $session;
        $this->translator = $translator;
    }

    protected function getKeys(){
        $captcha = new Captcha();

        $keys = $this->getDoctrine()->getRepository(Captcha::class)->getKeys();

        if(!$keys){
            if(in_array($this->translator->trans('Form sended without captcha...'), $this->session->getFlashBag()->get('warning'))){
                return false;
            }
            $this->addFlash('warning', $this->translator->trans('Form sended without captcha...'));
            return false;
        }
        return [$keys[0]->getPrivateKey(),$keys[0]->getPublicKey()];
    }

    public function enabled(){
        if($this->getKeys()){
            return true;
        }
        return false;
    }

    public function getPublicKey(){
        return $this->getKeys()[1];
    }

    public function verify($captcha){
        $keys = $this->getKeys();
        if($keys){
            $ReCaptchaValid = false;
            $url = "https://www.google.com/recaptcha/api/siteverify";
            $privateKey = $keys[0];
            $publicKey = $keys[1];

            $data = array(
                'secret' => $privateKey,
                'response' => $captcha,
                'remoteip' => $_SERVER['REMOTE_ADDR']
            );

            $curlConfig = array(
                CURLOPT_URL => $url,
                CURLOPT_POST => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POSTFIELDS => $data
            );

            $ch = curl_init();

            curl_setopt_array($ch, $curlConfig);

            $response = curl_exec($ch);
            curl_close($ch);

            $jsonResponse = json_decode($response);

            if($jsonResponse->success === true){
                $ReCaptchaValid = true;
            }

            return $ReCaptchaValid;
        }
        return true;
    }

    public function resetKeys(){
        $keys = $this->getDoctrine()->getRepository(Captcha::class)->getKeys();

        if($keys){
            $em = $this->getDoctrine()->getManager();
            $em->remove($keys[0]);
            $em->flush();
        }
    }
}