<?php

namespace VinilShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;


class FeedbackController extends Controller
{
    /**
     * @Route("/admin/feedbacks/list/{page}/{sort}/{direction}", name="admin_feedbacks")
     * @Template()
     */
    public function indexAction(Request $request, $page =1 , $sort = 'id', $direction='asc')
    {
        $feedbacks = $this
            ->getDoctrine()
            ->getRepository('VinilShopBundle:Feedback')
            ->findAll();
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $feedbacks,
            $page,
            50,
            ['defaultSortFieldName' => $sort,
                'defaultSortDirection' => $direction]
        );

        return[
            'pagination' => $pagination,
        ];
    }


    /**
     * @Route("/admin/feedback/show/{id}", name="admin_feedback_show")
     * @Template()
     */
    public function showAction(Request $request, $id)
    {

        $feedback = $this
            ->getDoctrine()
            ->getRepository('VinilShopBundle:Feedback')
            ->find($id);

        $feedback->setIsActive(false);

        $em = $this->getDoctrine()->getManager();
        $em->persist($feedback);
        $em->flush();

        return [
            'feedback' => $feedback
        ];

    }



}
