<?php
namespace VinilShopBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


class OrderController extends Controller
{

    /**
     * @Route("/order/create", name="order_create")
     * @Template()
     */
    public function createAction(Request $request)
    {
        return[];


    }

}