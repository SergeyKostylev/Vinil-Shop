<?php

namespace VinilShopBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;



class ProductController extends Controller
{
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

    /**
     * @Route("/products/category/{id}/{page}/{sort}/{direction}", name = "products_by_category")
     * @Template()
     */
    public function productsByCategoryAction(Request $request, $id, $page =1, $sort = 'name', $direction='asc')
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
     * @Route("/products/manufacturer/{manufacturerId}/category/{categoryId}/{page}/{sort}/{direction}", name = "product_manufacturer_by_categoty")
     * @Template()
     */
    public function productOfManufacturerByCategoryAction(Request $request, $categoryId, $manufacturerId, $page =1, $sort = 'name', $direction='asc')
    {
        $manufacturer = $this
            ->getDoctrine()
            ->getRepository('VinilShopBundle:Manufacturer')
            ->find($manufacturerId);

        if (!$manufacturer) {
            throw  $this->createNotFoundException('Производитель не найден');
        }
        $category = $this
            ->getDoctrine()
            ->getRepository('VinilShopBundle:Category')
            ->find($categoryId);

        if (!$category) {
            throw  $this->createNotFoundException('Категория не найдена');
        }

        $products = $this
            ->getDoctrine()
            ->getRepository('VinilShopBundle:Product')
            ->findBy([
                'category' => $category->getId(),
                'manufacturer' => $manufacturer->getId()
            ]);

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






}
