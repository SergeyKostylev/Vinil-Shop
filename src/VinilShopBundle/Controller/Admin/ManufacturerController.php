<?php

namespace VinilShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use VinilShopBundle\Entity\Manufacturer;
use VinilShopBundle\Form\ManufacturerType;
use Symfony\Component\HttpFoundation\Response;


class ManufacturerController extends Controller
{
    /**
     * @Route("/admin/manufacturer", name = "manufacturers")
     * @Template()
     */

    public function indexAction(Request $request)
    {
        $manufacturers = $this
            ->getDoctrine()
            ->getRepository('VinilShopBundle:Manufacturer')
            ->findAll();
        return['manufacturers'=>$manufacturers];
    }
    /**
     * @Route("/admin/manufacturer/add", name = "add_manufacturer")
     * @Template()
     */
    public function addAction(Request $request)
    {
        $manufacturer = new Manufacturer();
        $form = $this->createForm(ManufacturerType::class,$manufacturer);
        $form->add('submit',SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            $em = $this->getDoctrine()->getManager();
            $em->persist($manufacturer);
            $em->flush();
            return $this->redirectToRoute('manufacturer');
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
//        $em->remove($category);
//        $em->flush();
        return new Response(    'Content',
            Response::HTTP_OK);


    }

}
