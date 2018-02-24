<?php

namespace VinilShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use VinilShopBundle\Entity\Attribute;
use VinilShopBundle\Entity\Attribute_name;
use VinilShopBundle\Entity\GalleryImages;
use VinilShopBundle\Entity\Product;
use VinilShopBundle\Form\Attribute_nameType;
use VinilShopBundle\Form\AttributeType;
use VinilShopBundle\Form\ManufacturerType;
use VinilShopBundle\Form\ProductType;
use VinilShopBundle\Repository\AttributeRepository;
use VinilShopBundle\Service\FileUploader;


class ProductController extends Controller
{
    /**
     * @Route("/admin/product/list/{page}/{sort}/{direction}", name = "admin_products")
     * @Template()
     */
    public function indexAction(Request $request, $page =1 , $sort = 'name', $direction='asc')
    {
        $products = $this
            ->getDoctrine()
            ->getRepository('VinilShopBundle:Product')
            ->findAll();
        $categores = $this
            ->getDoctrine()
            ->getRepository('VinilShopBundle:Category')
            ->findTree();

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $products, /* query NOT result */
            $page,
//            $request->query->getInt('page', 1)/*page number*/,
            10,
                    ['defaultSortFieldName' => $sort,
                'defaultSortDirection' => $direction]
        );

        return[
            'pagination' => $pagination,
            'categores'=>$categores
        ];
    }

    /**
     * @Route("/admin/product/add", name = "add_product")
     * @Template()
     */
    public function addAction(Request $request)
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class,$product,['required_file' => true]);
        $form->add('submit',SubmitType::class);
        $form->add('new_manufacturer', ManufacturerType::class, ['mapped' => false, 'required' => false]);
        $form->add('attributes', CollectionType::class,array(
            'entry_type' => AttributeType::class,
            'allow_add' => true,
            'label' => false,
            'allow_delete' => true,
        ));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $galleryFiles = $product->getOtherImages();
            $em = $this->getDoctrine()->getManager();
            foreach($galleryFiles as $file){
                $fileUploader = new FileUploader($this->getParameter('gallery_img'));
                $fileName = $fileUploader->upload($file);
                $gallery_image = new GalleryImages();
                $gallery_image->setName($fileName)->setProduct($product);;
                $em->persist($gallery_image);
            }
            $fileUploader = new FileUploader($this->getParameter('img_entities_directory'));
            $file = $product->getTitleImage();
            $fileName = $fileUploader->upload($file);
            $product->setTitleImage($fileName);

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
     * @Route("/admin/product/category/{id}/{page}/{sort}/{direction}", name = "product_by_category")
     * @Template()
     */
    public function productByCategoryAction(Request $request, $id, $page =1 , $sort = 'name', $direction='asc')
    {
        $products = $this
            ->getDoctrine()
            ->getRepository('VinilShopBundle:Product')
            ->findByCategory($id);

        $category = $this
            ->getDoctrine()
            ->getRepository('VinilShopBundle:Category')
            ->find($id);

        $categores = $this
            ->getDoctrine()
            ->getRepository('VinilShopBundle:Category')
            ->findTree();

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $products, /* query NOT result */
            $page,
//            $request->query->getInt('page', 1)/*page number*/,
            10,
            ['defaultSortFieldName' => $sort,
                'defaultSortDirection' => $direction]
        );
        return [
            'pagination' => $pagination,
            'category' => $category,
            'categores' => $categores
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
        $category_id = $product->getCategory()->getID();

        $attributes = $this
            ->getDoctrine()
            ->getRepository('VinilShopBundle:Attribute_name')
            ->attributesOfCategory($category_id);
        $amount_attributes = count($attributes);

        if(!$product) {
            throw  $this->createNotFoundException('Товар не найден');
        }
        $form =$this->createForm(ProductType::class, $product);
        $form->add('new_manufacturer', ManufacturerType::class, ['mapped' => false, 'required' => false ]);
        $form->add('attributes', CollectionType::class,array(
            'entry_type' => AttributeType::class,
            'allow_add' => true,
            'label' => false,
            'allow_delete' => true,
            'entry_options'=> array(
                'category_id' => $product->getCategory()->getId()
            )));
        $form->add('submit', SubmitType::class);
        $form->remove('category');
        $currentFile = $product->getTitleImage();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            $galleryFiles = $form['otherImages']->getData();
            $em = $this->getDoctrine()->getManager();

            foreach($galleryFiles as $file){
                $fileUploader = new FileUploader($this->getParameter('gallery_img'));
                $fileName = $fileUploader->upload($file);
                $gallery_image = new GalleryImages();
                $gallery_image->setName($fileName)->setProduct($product);;
                $em->persist($gallery_image);
            }

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
            $newManufacturer= $form->get('new_manufacturer')->getData();
            if ($newManufacturer){
                $product->setManufacturer($newManufacturer);
                $em->persist($newManufacturer);
            }
            $em->persist($product);
            $em->flush();
            return $this->redirectToRoute('admin_products');
        }
        return [
            'product' => $product,
            'amount_attributes' => $amount_attributes,
            'form' => $form->createView()
        ];
    }






}
