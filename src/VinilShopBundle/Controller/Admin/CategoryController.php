<?php

namespace VinilShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use VinilShopBundle\Entity\Category;
use VinilShopBundle\Form\CategoryType;

class CategoryController extends Controller
{
    /**
     * @Route("/admin/category")
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
        $categories = $this
            ->getDoctrine()
            ->getRepository('VinilShopBundle:Category')
            ->parentCategories()
            ;

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

}
