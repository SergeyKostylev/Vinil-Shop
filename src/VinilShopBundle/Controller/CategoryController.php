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

        return['categoryes' => $categoryes];
    }

    /**
     * @Route("/categoryes/parent/{id}", name = "children_categotyes")
     * @Template()
     */
    public function childrenCategoryesAction(Request $request, $id)
    {
        $category = $this
            ->getDoctrine()
            ->getRepository('VinilShopBundle:Category')
            ->find($id);
        if (!$category){
            throw  $this->createNotFoundException('Категория не найдена');
        }

        if (count($category->getChildren())){

            //there are child categories
            $categoryes = $this
                ->getDoctrine()
                ->getRepository('VinilShopBundle:Category')
                ->childrenCategories($id);
            return ['categoryes' => $categoryes];
        }
        else{
            //no child categories
            return $this->redirectToRoute('products_by_category', ['id' => $id]);
        }
    }

    /**
     * @Route("/categoryes/manufacturer/{id}/", name = "categoryes_manufacturer")
     * @Template()
     */
    public function catergoryesByManufacturerAction(Request $request, $id)
    {
        $manufacturer = $this
            ->getDoctrine()
            ->getRepository('VinilShopBundle:Manufacturer')
            ->find($id);

        if (!$manufacturer) {
            throw  $this->createNotFoundException('Производитель не найден');
        }
        $categoryes = $this
            ->getDoctrine()
            ->getRepository('VinilShopBundle:Category')
            ->categoryByManufacturer($id);
        return [
            'categoryes' => $categoryes,
            'manufacturer' => $manufacturer
        ];
    }

}