<?php

namespace VinilShopBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class ManufacturerController extends Controller
{
    /**
     * @Route("/manufacturers/list", name="manufacturers-list")
     * @Template()
     */

    public function indexAction()
    {
        $manufacturers = $this
            ->getDoctrine()
            ->getRepository('VinilShopBundle:Manufacturer')
            ->findBy([],['name'=>'asc']);

        return ['manufacturers' => $manufacturers];
    }


}
