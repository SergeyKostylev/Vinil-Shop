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
            return new Response(    'Товар не найден',
                Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        $titleImage = $product->getTitleImage();
        $galleryImages = $product->getGalleryImages();

        $em->remove($product);
        $em->flush();

        if (count($galleryImages)) {
            foreach ($galleryImages as $images) {
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
        $category = $this
            ->getDoctrine()
            ->getRepository('VinilShopBundle:Category')
            ->find($category_id);

        if (!$category) {
            return new Response('Category does not exist',
                Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $attributes = $this
            ->getDoctrine()
            ->getRepository('VinilShopBundle:Attribute_name')
            ->attributesOfCategory($category_id);

        $collection = [];
        foreach ($attributes as $attrib ) {
            /**
             * @var Attribute_name $attrib
             */
            array_push($collection , [
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
            return new Response(    'Изображение не найдено',
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
            return new Response(    'Изображение не найдено',
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        $nameImage = $image->getImage();

        $em = $this->getDoctrine()->getManager();

        $em->remove($image);
        $em->flush();
        @unlink($this->getParameter('advertising_slider_images') . '/' .$nameImage);

        return new Response(    'Ок',
            Response::HTTP_OK
        );
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
            return new Response(    'Изображение не найдено',
                Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        $em = $this->getDoctrine()->getManager();
        $em->remove($feedback);
        $em->flush();
        return new Response(    'Ок',
            Response::HTTP_OK
        );
    }

    /**
     * @Route("/admin/odrer/status/edit/{order_id}/{status_id}", name = "order_status_edit")
     */

    public function editStatusOrder(Request $request, $order_id, $status_id)
    {
        $order = $this
            ->getDoctrine()
            ->getRepository('VinilShopBundle:Orders')
            ->find($order_id);

        $state = $this
            ->getDoctrine()
            ->getRepository('VinilShopBundle:State')
            ->find($status_id);

        if (!$order || !$state) {
            return new Response(    'Заказ/Статус не найден',
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
        $order->setState($state);

        $active = (in_array($state->getId() , [5,7])) ? false : true ;

        $em = $this->getDoctrine()->getManager();
        $em->persist($order);
        $em->flush();

        return new JsonResponse([
            'active' => $active
        ],200);

    }

    /**
     * @Route("/amount-active-feedback", name="amount_active_feedback")
     *
     */
    public function amountInCart()
    {
        $feedbacks = $this
            ->getDoctrine()
            ->getRepository('VinilShopBundle:Feedback')
            ->findBy([
                'isActive' => true
            ]);
        $amount = count($feedbacks);
            return  new JsonResponse([
                'amount' => $amount
            ],200);
    }
}
