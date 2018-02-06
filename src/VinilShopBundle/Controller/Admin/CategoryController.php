<?php

namespace VinilShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use VinilShopBundle\Entity\Category;
use VinilShopBundle\Form\CategoryType;
use Symfony\Component\HttpFoundation\Response;

class CategoryController extends Controller
{
    /**
     * @Route("/admin/category", name = "categoryes")
     * @Template()
     */

    public function indexAction(Request $request)
    {
        $categores = $this
            ->getDoctrine()
            ->getRepository('VinilShopBundle:Category')
            ->findTree();
        return['categores'=>$categores];
    }
    /**
     * @Route("/admin/category/add", name = "add_category")
     * @Template()
     */
    public function addAction(Request $request)
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class,$category);
        $form->add('submit',SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();
            return $this->redirectToRoute('add_category');

        }
        return [
            'form' => $form->createView()
        ];
    }
    /**
     * @Route("/admin/category/edit/{id}", name = "edit_category")
     * @Template()
     */
    public function editAction(Request $request, $id)
    {
        $category = $this
            ->getDoctrine()
            ->getRepository('VinilShopBundle:Category')
            ->find($id);
        if(!$category) {
            throw  $this->createNotFoundException('Category not found');
        }

        $form = $this->createForm(CategoryType::class,$category);
        $form->add('submit',SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();
            return $this->redirectToRoute('categoryes');
        }

        return[ 'category'=>$category,
                'form' => $form->createView()];

    }


    /**
     * @Route("/admin/category/delete/{id}", name = "delete_category")
     */
    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $category = $this
            ->getDoctrine()
            ->getRepository('VinilShopBundle:Category')
            ->find($id);


        if (!$category) {
            throw  $this->createNotFoundException('Категория не найдена');
            return new Response(    'Ops',
                Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        $em->remove($category);
        $em->flush();
        dump($category);die;
        return new Response(    'Content',
            Response::HTTP_OK);


    }

}
