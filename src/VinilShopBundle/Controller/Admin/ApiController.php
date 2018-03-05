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

        if (!$product) {
            throw  $this->createNotFoundException('Товар не найден');
            return new Response(    'Ops',
                Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        $titleImage = $product->getTitleImage();
        $galleryImages = $product->getGalleryImages();

        $em->remove($product);
        $em->flush();

        if(count($galleryImages)) {
            foreach ($galleryImages as $images){
                $nameImage = $images->getName();
                @unlink($this->getParameter('gallery_img') . '/' .$nameImage);
            }
        }
        @unlink($this->getParameter('img_entities_directory') . '/' .$titleImage);

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

    /**
     * @Route("/admin/gallery-image/delete/{id}", name = "delete_gallery_image")
     */

    public function galleryImageAction(Request $request, $id)
    {
        $image = $this
            ->getDoctrine()
            ->getRepository('VinilShopBundle:GalleryImages')
            ->find($id);

        if (!$image) {
            throw  $this->createNotFoundException('Изображение не найдено');
            return new Response(    'Ops',
                Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        $nameImage = $image->getName();

        $em = $this->getDoctrine()->getManager();

        $em->remove($image);
        $em->flush();

        @unlink($this->getParameter('gallery_img') . '/' .$nameImage);

        return new Response(    'Content',
            Response::HTTP_OK);

    }

    /**
     * @Route("/admin/advertising/delete/{id}", name = "delete_advertising_image")
     */

    public function deleteAdvertisingSlidrImageAction(Request $request, $id)
    {
        $image = $this
            ->getDoctrine()
            ->getRepository('VinilShopBundle:Advertising_slider')
            ->find($id);

        if (!$image) {
            throw  $this->createNotFoundException('Изображение не найдено');
            return new Response(    'Ops',
                Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $nameImage = $image->getImage();

        $em = $this->getDoctrine()->getManager();

        $em->remove($image);
        $em->flush();
        @unlink($this->getParameter('advertising_slider_images') . '/' .$nameImage);

        return new Response(    'Ок',
            Response::HTTP_OK);

    }
    /**
     * @Route("/admin/feedback/delete/{id}", name = "delete_feedback")
     */

    public function deleteFeedbackAction(Request $request, $id)
    {
        $feedback = $this
            ->getDoctrine()
            ->getRepository('VinilShopBundle:Feedback')
            ->find($id);


        if (!$feedback) {
            throw  $this->createNotFoundException('Изображение не найдено');
            return new Response(    'Ops',
                Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        $em = $this->getDoctrine()->getManager();
        $em->remove($feedback);
        $em->flush();
        return new Response(    'Ок',
            Response::HTTP_OK);

    }




}
