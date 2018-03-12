<?php
namespace VinilShopBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller
{

    /**
     * @Route("/user/private-office", name="private_office")
     * @Template()
     */
    public function officeAction(Request $request)
    {
        $user = $this->getUser();
        if(!$user)
        {
            return $this->redirectToRoute('home_page');
        }
        $user = $this
            ->getDoctrine()
            ->getRepository('VinilShopBundle:User')
            ->find($user->getId());

        $active_orders = $this
            ->getDoctrine()
            ->getRepository('VinilShopBundle:Orders')
            ->findBy([
                'user' => $user->getId(),
                'state' => [1,2,3,4]
                ]);

        return [
            'user' => $user,
            'active_orders' => $active_orders

        ];

    }

    /**
     * @Route("/user/all-orders", name="all_orders")
     * @Template()
     */
    public function allUserOrdersAction(Request $request)
    {
        $user = $this->getUser();
        if(!$user)
        {
            return $this->redirectToRoute('home_page');
        }
        $user = $this
            ->getDoctrine()
            ->getRepository('VinilShopBundle:User')
            ->find($user->getId());

        $orders = $this
            ->getDoctrine()
            ->getRepository('VinilShopBundle:Orders')
            ->findBy([
                'user' => $user->getId()
            ]);

        return [
            'user' => $user,
            'orders' => $orders

        ];

    }

    /**
     * @Route("/user/order/{id}", name="user_order")
     * @Template()
     */
    public function userorderAction(Request $request, $id)
    {
        $user = $this->getUser();
        if(!$user)
        {
            return $this->redirectToRoute('home_page');
        }
        $user = $this
            ->getDoctrine()
            ->getRepository('VinilShopBundle:User')
            ->find($user->getId());

        $order = $this
            ->getDoctrine()
            ->getRepository('VinilShopBundle:Orders')
            ->find($id);

        return [
            'user' => $user,
            'order' => $order

        ];
    }

}