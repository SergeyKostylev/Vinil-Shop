<?php

namespace VinilShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Regex;
use VinilShopBundle\Form\StateType;


class OrderController extends Controller
{
    /**
     * @Route("/admin/orders/list/{page}/{sort}/{direction}", name = "admin_orders_list")
     * @Template()
     */

    public function indexAction(Request $request, $page =1 , $sort = 'id', $direction='asc')
    {
        $search = $request->get('search');

        if($search != 'all' && !preg_match('/^[ ]+$/ u', $search)){

            $orders = $this
                ->getDoctrine()
                ->getRepository('VinilShopBundle:Orders')
                ->serchForNumber($search);
        }else{
        $orders = $this
            ->getDoctrine()
            ->getRepository('VinilShopBundle:Orders')
            ->findAll();
        }

        $states = $this
            ->getDoctrine()
            ->getRepository('VinilShopBundle:State')
            ->findAll();

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $orders,
            $page,
            50,
            ['defaultSortFieldName' => $sort,
                'defaultSortDirection' => $direction]
        );
        return[
            'pagination' => $pagination,
            'states' => $states
        ];
    }


    /**
     * @Route("/admin/orders/user/{id}/{page}/{sort}/{direction}", name = "admin_user_orders")
     * @Template()
     */

    public function ordersByUserAction(Request $request, $id,  $page =1 , $sort = 'id', $direction='asc')
    {
        $user = $this
              ->getDoctrine()
              ->getRepository('VinilShopBundle:User')
              ->find($id);
        if(!$user){
            throw  $this->createNotFoundException('Пользователь не найден');
        }
        $orders = $this
            ->getDoctrine()
            ->getRepository('VinilShopBundle:Orders')
            ->findBy([
                'user' => $user->getId()
            ]);

        $states = $this
            ->getDoctrine()
            ->getRepository('VinilShopBundle:State')
            ->findAll();

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $orders,
            $page,
            50,
            ['defaultSortFieldName' => $sort,
                'defaultSortDirection' => $direction]
        );

        return[
            'user' => $user,
            'pagination' => $pagination,
            'states' => $states
        ];
    }

    /**
     * @Route("/admin/order/show/{id}", name = "admin_order_show")
     * @Template()
     */

    public function showAction(Request $request, $id)
    {
        $order = $this
            ->getDoctrine()
            ->getRepository('VinilShopBundle:Orders')
            ->find($id);

        if(!$order){
            throw  $this->createNotFoundException('Пользователь не найден');
        }
        $states = $this
            ->getDoctrine()
            ->getRepository('VinilShopBundle:State')
            ->findAll();

        return [
            'order' => $order,
            'states' => $states
        ];
    }


}
