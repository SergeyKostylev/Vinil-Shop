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

    /**
     * @Route("/categoryes/parent/{id}", name = "children_categotyes")
     * @Template()
     */

    public function childrenCategoryesAction(Request $request, $id)
    {
        $categoryes = $this
            ->getDoctrine()
            ->getRepository('VinilShopBundle:Category')
            ->childrenCategories($id);

        return ['categoryes' => $categoryes];
    }
}
