<?php

namespace VinilShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use VinilShopBundle\Entity\Attribute_name;
use VinilShopBundle\Form\Attribute_nameType;
use VinilShopBundle\Form\AttributeType;

class AttributesController extends Controller
{
    /**
     * @Route("/admin/attributes", name = "attributes")
     * @Template()
     */

    public function indexAction(Request $request)
    {
        $attributes = $this
            ->getDoctrine()
            ->getRepository('VinilShopBundle:Attribute_name')
            ->findBy([],['name' => 'ASC']);
        return['attributes'=>$attributes];
    }

    /**
     * @Route("/admin/attribute/add", name = "add_attribute")
     * @Template()
     */
    public function addAction(Request $request)
    {
        $attribute = new Attribute_name();
        $form = $this->createForm(Attribute_nameType::class,$attribute);
        $form->add('submit',SubmitType::class);
        $form->handleRequest($request);
        $attributes = $this
            ->getDoctrine()
            ->getRepository('VinilShopBundle:Attribute_name')
            ->findByName($attribute->getName());

        if ($form->isSubmitted() && $form->isValid() && !$attributes) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($attribute);
            $em->flush();
            return $this->redirectToRoute('add_attribute');
        }
        return [
            'form' => $form->createView()
        ];
    }

}
