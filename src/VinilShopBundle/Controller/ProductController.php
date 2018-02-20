<?php

namespace VinilShopBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;



class ProductController extends Controller
{
    /**
     * @Route("/products", name = "products-list")
     * @Template()
     */

    public function indexAction(Request $request)
    {
        $products = $this
            ->getDoctrine()
            ->getRepository('VinilShopBundle:Product')
            ->findAll();
        dump($products);
        return['products'=>$products];
    }


    /**
     * @Route("/product/show/{id}", name = "product-show")
     * @Template()
     */
    public function showAction(Request $request, $id)
    {
        $product = $this
            ->getDoctrine()
            ->getRepository('VinilShopBundle:Product')
            ->find($id);
        $category_id = $product->getCategory()->getID();

        dump(count($product->getGalleryImages()));

        $attributes = $this
            ->getDoctrine()
            ->getRepository('VinilShopBundle:Attribute_name')
            ->attributesOfCategory($category_id);
        $amount_attributes = count($attributes);

        if(!$product) {
            throw  $this->createNotFoundException('Товар не найден');
        }


        return [
            'product' => $product,
            'amount_attributes' => $amount_attributes,
        ];
    }






}
