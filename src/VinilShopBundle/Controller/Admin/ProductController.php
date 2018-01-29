<?php

namespace VinilShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use VinilShopBundle\Entity\Product;
use VinilShopBundle\Form\ProductType;


class ProductController extends Controller
{
    /**
     * @Route("/admin/product", name = "products")
     * @Template()
     */

    public function indexAction(Request $request)
    {
        $products = $this
            ->getDoctrine()
            ->getRepository('VinilShopBundle:Product')
            ->findAll();
        return['products'=>$products];
    }
    /**
     * @Route("/admin/product/add", name = "add_product")
     * @Template()
     */
    public function addAction(Request $request)
    {
        $max_article = $this
            ->getDoctrine()
            ->getRepository('VinilShopBundle:Product')
            ->articleMaxValue();

        $product = new Product();
        $form = $this->createForm(ProductType::class,$product);
        $form->get('article')->setData(++$max_article);
        $form->add('submit',SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();
            return $this->redirectToRoute('add_product');
        }
        return [
            'form' => $form->createView()
        ];
    }



}
