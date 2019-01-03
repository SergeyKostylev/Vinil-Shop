<?php

namespace VinilShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller
{
    /**
     * @Route("/admin/users/list/{page}/{sort}/{direction}", name = "admin_users")
     * @Template()
     */
    public function indexAction(Request $request, $page =1 , $sort = 'id', $direction='asc')
    {
        $users = $this
            ->getDoctrine()
            ->getRepository('VinilShopBundle:User')
            ->findAll();

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $users,
            $page,
            20,
            [
                'defaultSortFieldName' => $sort,
                'defaultSortDirection' => $direction
            ]
        );

        return[
            'pagination' => $pagination
        ];
    }
}
