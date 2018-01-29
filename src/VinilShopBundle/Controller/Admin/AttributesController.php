<?php

namespace VinilShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;


class AttributesController extends Controller
{
    /**
     * @Route("/admin/attributes", name = "attributes")
     * @Template()
     */

    public function indexAction(Request $request)
    {
        $attributes = $this
            ->getDoctrine()
            ->getRepository('VinilShopBundle:Attribute_name')
            ->findAll();
//        dump($attributes);die;
        return['attributes'=>$attributes];
    }
    /**
     * @Route("/admin/category/add", name = "add_category")
     * @Template()
     */
//    public function addAction(Request $request)
//    {
//        $category = new Category();
//        $form = $this->createForm(CategoryType::class,$category);
//        $form->add('submit',SubmitType::class);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $em = $this->getDoctrine()->getManager();
//            $em->persist($category);
//            $em->flush();
//            return $this->redirectToRoute('add_category');
//
//        }
//        return [
//            'form' => $form->createView()
//        ];
//    }



}
