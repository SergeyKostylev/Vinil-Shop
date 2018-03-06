<?php
namespace VinilShopBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


class UserController extends Controller
{

    /**
     * @Route("/user/{id}/private-office", name="private_office")
     * @Template()
     */
    public function officeAction(Request $request, $id)
    {
        $user = $this
            ->getDoctrine()
            ->getRepository('VinilShopBundle:User')
            ->find($id);
        if(!$user)
        {
            throw  $this->createNotFoundException('Пользователь не найден');
        }
        return [];


    }
}