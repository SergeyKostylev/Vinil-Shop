<?php

namespace VinilShopBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use VinilShopBundle\Entity\User;

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

        if (!$product) {
            throw  $this->createNotFoundException('Товар не найден');
        }
        /**
         * @var User $user
         */
        $user = $this->getUser();

        if ($user) {
            $inCart = $this
                ->getDoctrine()
                ->getRepository('VinilShopBundle:Cart')
                ->findBy([
                    'user' => $user->getId(),
                    'product' => $id
                ]);
        } else {
            $session = new Session();
            $cart = $session->get('cart');
            @$inCart = array_key_exists($id, $cart) ? true : false;
        }
        return [
            'product' => $product,
            'inCart' => $inCart
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
            [
                'defaultSortFieldName' => $sort,
                'defaultSortDirection' => $direction
            ]
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
            [
                'defaultSortFieldName' => $sort,
                'defaultSortDirection' => $direction
            ]
        );
        return [
            'pagination' => $pagination
        ];
    }

    /**
     * @Route("/products/search/{page}/{sort}/{direction}", name = "products_search")
     * @Template()
     */
    public function productsSearchAction(Request $request,  $page = 1, $sort = 'name', $direction='asc', $search = 'all')
    {
        $search = $request->get('search');
        $search_active = ($search == 'all' || $search == '') ? false : true;

        if ($search != 'all' && !preg_match('/^[ ]+$/ u', $search)) {

            $products = $this
                ->getDoctrine()
                ->getRepository('VinilShopBundle:Product')
                ->serchForName($search);
        } else {
            $products = $this
                ->getDoctrine()
                ->getRepository('VinilShopBundle:Product')
                ->findAll();
        }

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $products,
            $page,
            9,
            [
                'defaultSortFieldName' => $sort,
                'defaultSortDirection' => $direction
            ]
        );

        return [
            'pagination' => $pagination,
            'search_active' => $search_active
        ];
    }
}