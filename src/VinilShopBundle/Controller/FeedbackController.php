<?php

namespace VinilShopBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use VinilShopBundle\Service\CapthaService;

class FeedbackController extends Controller
{
    /**
     * @Route("/feedback", name="feedback")
     * @Template()
     */

    public function indexAction()
    {
        return [];
    }

    /**
     * @Route("/feedback/captcha", name="feedback_captcha")
     *
     */

    public function captchaAction()
    {
        $number = rand(1000,9999);

        $session = $this->get('session');

        $session->set('feedback_captcha', $number);

//        dump($session->get('feedback_capthca'));die;

        $font = $this->getParameter('captcha_font_file');

        $captcha = new CapthaService($number,$font);
        $response = new Response();
        $response->setContent($captcha->output());
        $response->setStatusCode(Response::HTTP_OK);
        $response->headers->set('Content-Type', 'image/png');
        $response->send();
    }



}
