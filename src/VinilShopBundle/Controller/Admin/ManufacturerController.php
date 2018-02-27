<?php

namespace VinilShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use VinilShopBundle\Entity\Manufacturer;
use VinilShopBundle\Form\ManufacturerType;
use Symfony\Component\HttpFoundation\Response;
use VinilShopBundle\Service\FileUploader;


class ManufacturerController extends Controller
{
    /**
     * @Route("/admin/manufacturer", name = "admin_manufacturers")
     * @Template()
     */

    public function indexAction(Request $request)
    {
        $manufacturers = $this
            ->getDoctrine()
            ->getRepository('VinilShopBundle:Manufacturer')
            ->findBy([],['name'=>'ASC']);
        return['manufacturers'=>$manufacturers];
    }
    /**
     * @Route("/admin/manufacturer/add", name = "add_manufacturer")
     * @Template()
     */
    public function addAction(Request $request)
    {
        $manufacturer = new Manufacturer();
        $form = $this->createForm(ManufacturerType::class,$manufacturer,['required_file' => true]);
        $form->add('submit',SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $fileUploader = new FileUploader($this->getParameter('manufacturer_log'));
            $file = $manufacturer->getTitleImage();
            $fileName = $fileUploader->upload($file);
            $manufacturer->setTitleImage($fileName);

            $em = $this->getDoctrine()->getManager();
            $em->persist($manufacturer);
            $em->flush();
            return $this->redirectToRoute('add_manufacturer');

        }
        return [
            'form' => $form->createView()
        ];
    }

    /**
     * @Route("/admin/manufacturer/edit/{id}", name = "edit_manufacturer")
     * @Template()
     */
    public function editAction(Request $request, $id)
    {
        $manufacturer = $this
            ->getDoctrine()
            ->getRepository('VinilShopBundle:Manufacturer')
            ->find($id);
        if(!$manufacturer) {
            throw  $this->createNotFoundException('Manufacturer not found');
        }

        $form = $this->createForm(ManufacturerType::class,$manufacturer);
        $form->add('submit',SubmitType::class);

        $currentFile = $manufacturer->getTitleImage();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            /**
             * @var UploadedFile $file
             */
            $file = $form['titleImage']->getData();
            if(!empty($file)) {
                $fileUploader = new FileUploader($this->getParameter('manufacturer_log'));
                $file = $manufacturer->getTitleImage();
                $fileName = $fileUploader->upload($file);
                $manufacturer->setTitleImage($fileName);
                if (!empty($currentFile)){
                    @unlink($this->getParameter('manufacturer_log') . '/' .$currentFile);
                }
            }else{
                if (!empty($currentFile)){
                    $manufacturer->setTitleImage($currentFile);
                }
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($manufacturer);
            $em->flush();
            return $this->redirectToRoute('admin_manufacturers');
        }

        return[ 'manufacturer'=>$manufacturer,
                'form' => $form->createView()];

    }
    /**
     * @Route("/admin/manufacturer/delete/{id}", name = "delete_manufacturer")
     */
    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $manufacturer = $this
            ->getDoctrine()
            ->getRepository('VinilShopBundle:Manufacturer')
            ->find($id);

        if (!$manufacturer) {
            throw  $this->createNotFoundException('Производитель не найдена');
            return new Response(    'Ops',
                Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $manuf_products = $this
            ->getDoctrine()
            ->getRepository('VinilShopBundle:Product')
            ->productOfManufacturer($id);

        if($manuf_products){

            return new JsonResponse([
                'prodExist' => true
            ],500);


        }

        $em->remove($manufacturer);
        $em->flush();
        dump($em);die();
        return new Response(    'Content',
            Response::HTTP_OK);

    }

}
