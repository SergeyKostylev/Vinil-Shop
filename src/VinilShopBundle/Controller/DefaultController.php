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

    /**
     * @Route("/payment", name="payment_page")
     * @Template()
     */
    public function paymentAction()
    {
        return [];
    }

    /**
     * @Route("/delivery", name="delivery_page")
     * @Template()
     */
    public function deliveryAction()
    {
        return [];
    }

    /**
     * @Route("/info", name="info_page")
     * @Template()
     */
    public function infoAction()
    {
        return [];
    }
}