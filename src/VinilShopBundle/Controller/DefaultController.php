<?php

namespace VinilShopBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Session\Session;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="home_page")
     * @Template()
     */

    public function indexAction()
    {
        $products = $this
            ->getDoctrine()
            ->getRepository('VinilShopBundle:Product')
            ->productRandLimin();

        $slider = $this
            ->getDoctrine()
            ->getRepository('VinilShopBundle:Advertising_slider')
            ->findAll();

        return ['products' => $products,
                'slider' => $slider
        ];
    }



//    /**
//     * @Route("/ww", name="ww")
//     * @Template()
//     */
//
//    public function wwAction()
//    {
//
//        $message = \Swift_Message::newInstance()
//            ->setSubject('Contact from symblog')
//            ->setFrom($this->container->getParameter('mailer_user'))
//            ->setTo('s03540@gmail.com')
//            ->setBody('1212');
//        $this->get('mailer')->send($message);
//
//
//        return $this->redirectToRoute('home_page');
//    }




}
