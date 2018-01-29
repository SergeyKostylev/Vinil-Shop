<?php

namespace VinilShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use VinilShopBundle\Entity\Manufacturer;
use VinilShopBundle\Form\ManufacturerType;


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

        return[ 'category'=>$manufacturer,
                'form' => $form->createView()];

    }

}
