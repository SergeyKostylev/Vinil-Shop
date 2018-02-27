<?php

namespace VinilShopBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;



class ProductController extends Controller
{


    /**
     * @Route("/products/category/{id}/{page}/{sort}/{direction}", name = "products_by_category")
     * @Template()
     */
    public function productsByCategoryAction(Request $request, $id, $page =1 , $sort = 'name', $direction='asc')
    {
        $products = $this
            ->getDoctrine()
            ->getRepository('VinilShopBundle:Product')
            ->findByCategory($id);

        $category = $this
            ->getDoctrine()
            ->getRepository('VinilShopBundle:Category')
            ->find($id);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $products,
            $page,
            9,
            ['defaultSortFieldName' => $sort,
                'defaultSortDirection' => $direction]
        );
        return [
            'pagination' => $pagination
        ];
    }

    /**
     * @Route("/product/show/{id}", name = "product_show")
     * @Template()
     */
    public function showAction(Request $request, $id)
    {
        $product = $this
            ->getDoctrine()
            ->getRepository('VinilShopBundle:Product')
            ->find($id);

        if(!$product) {
            throw  $this->createNotFoundException('Товар не найден');
        }

        return [
            'product' => $product
        ];
    }






}
