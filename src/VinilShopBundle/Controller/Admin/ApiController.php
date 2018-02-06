<?php

namespace VinilShopBundle\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use VinilShopBundle\Entity\Attribute_name;
use VinilShopBundle\Entity\Category;

class ApiController extends Controller
{

    /**
     * @Route("/admin/product/delete/{id}", name = "delete_product")
     */

    public function deleteAction(Request $request, $id)
    {

        $em = $this->getDoctrine()->getManager();
        $product = $this
            ->getDoctrine()
            ->getRepository('VinilShopBundle:Product')
            ->find($id);
//        dump($product);die;
//
        if (!$product) {
            throw  $this->createNotFoundException('Товар не найден');

            return new Response(    'Ops',
                Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        $em->remove($product);
        $em->flush();

        return new Response(    'Content',
            Response::HTTP_OK);


    }
    /**
     * @Route("/admin/category/attributes/{category_id}", name = "attributes_of_category")
     */
    public function getAllAtyributesOfProductAction(Request $request, $category_id)
    {
        $category = new Category();
        $category = $this
            ->getDoctrine()
            ->getRepository('VinilShopBundle:Category')
            ->find($category_id);

        if (!$category){
            return new Response('Category does not exist',
                Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $attributes = $this
            ->getDoctrine()
            ->getRepository('VinilShopBundle:Attribute_name')
            ->attributesOfCategory($category_id);

        $collection=[];
        foreach ($attributes as $attrib )
        {
            /**
             * @var Attribute_name $attrib
             */
            array_push($collection ,
                [
                    'id' => $attrib->getId(),
                    'name' => $attrib->getName()
                ]);
        }
        return new JsonResponse($collection
            ,200);

    }



}
