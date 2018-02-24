<?php

namespace VinilShopBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;



class CategoryController extends Controller
{
    /**
     * @Route("/categoryes/list", name = "categotyes_list")
     * @Template()
     */

    public function indexAction(Request $request)
    {
        $categoryes = $this
            ->getDoctrine()
            ->getRepository('VinilShopBundle:Category')
            ->firstParentCategories();
//        dump($categoryes);
        return['categoryes'=>$categoryes];
    }

}
