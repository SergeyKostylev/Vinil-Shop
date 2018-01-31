<?php

namespace VinilShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use VinilShopBundle\Entity\Product;
use VinilShopBundle\Form\ManufacturerType;
use VinilShopBundle\Form\ProductType;
use VinilShopBundle\Service\FileUploader;


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
        $product = new Product();
        $form = $this->createForm(ProductType::class,$product);
        $form->add('submit',SubmitType::class);
        $form->add('new_manufacturer', ManufacturerType::class, ['mapped' => false, 'required' => false]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $fileUploader = new FileUploader($this->getParameter('img_entities_directory'));
            $file = $product->getTitleImage();
            $fileName = $fileUploader->upload($file);
            $product->setTitleImage($fileName);

            $em = $this->getDoctrine()->getManager();

            $newManufacturer= $form->get('new_manufacturer')->getData();
            if ($newManufacturer){
                $product->setManufacturer($newManufacturer);
                $em->persist($newManufacturer);
            }
            $em->persist($product);
            $em->flush();
            return $this->redirectToRoute('add_product');
        }
        return [
            'form' => $form->createView()
        ];
    }

    /**
     * @Route("/admin/product/edit/{id}", name = "edit_product")
     * @Template()
     */
    public function editAction(Request $request, $id)
    {
        $product = $this
            ->getDoctrine()
            ->getRepository('VinilShopBundle:Product')
            ->find($id);

        if(!$product) {
            throw  $this->createNotFoundException('Товар не найден');
        }

        $form =$this->createForm(ProductType::class, $product);
        $form->add('new_manufacturer', ManufacturerType::class, ['mapped' => false, 'required' => false]);
        $form->add('submit', SubmitType::class);
        $currentFile = $product->getTitleImage();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            /**
             * @var UploadedFile $file
             */
            $file = $form['titleImage']->getData();
            if(!empty($file)) {
                $fileUploader = new FileUploader($this->getParameter('img_entities_directory'));
                $file = $product->getTitleImage();
                $fileName = $fileUploader->upload($file);
                $product->setTitleImage($fileName);
                if (!empty($currentFile)){
                   @unlink($this->getParameter('img_entities_directory') . '/' .$currentFile);
                }
            }else{
                if (!empty($currentFile)){
                    $product->setTitleImage($currentFile);
                }
            }

            $em = $this->getDoctrine()->getManager();
            $newManufacturer= $form->get('new_manufacturer')->getData();
            if ($newManufacturer){
                $product->setManufacturer($newManufacturer);
                $em->persist($newManufacturer);
            }
            $em->persist($product);
            $em->flush();
            return $this->redirectToRoute('products');
        }
        return [
            'product' => $product,
            'form' => $form->createView()
        ];
    }
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
//        $em->remove($product);
//        $em->flush();

        return new Response(    'Content',
            Response::HTTP_OK);


    }




}
