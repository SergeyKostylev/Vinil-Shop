<?php
namespace VinilShopBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use VinilShopBundle\Entity\Cart;
use VinilShopBundle\Entity\User;

class CartController extends Controller
{
    /**
     * @Route("/cart/list", name="cart-list")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();

        $cart = [];
        $notActive = [];
        $sum = 0;

        if (!$user) {
            $session = new Session();
            if ($session->has('cart')) {
                $session_cart = $session->get('cart');

                foreach ($session_cart as $product_id => $amount ) {
                    $product = $this
                        ->getDoctrine()
                        ->getRepository('VinilShopBundle:Product')
                        ->find($product_id);
                    if ($product->getIsActive()) {
                        $cart[] = [
                            'product' => $product,
                            'amount' => $amount
                        ];
                        $sum+= $product->getPrice() * $amount;
                    } else {
                        $notActive[] = [
                            'product' => $product,
                            'amount' => $amount
                        ];
                    }
                }
            }

            return [
                'cart' => $cart,
                'sum' => $sum,
                'notActive' => $notActive
            ];
        }

        $all_cart = $this
            ->getDoctrine()
            ->getRepository('VinilShopBundle:Cart')
            ->findBy(['user' =>  $user->getId()]);

        foreach ($all_cart as $cart_item) {
            if ($cart_item->getProduct()->getIsActive()) {
                $cart[] = $cart_item;
                $price = $cart_item->getProduct()->getPrice();
                $sum+= $price * $cart_item->getAmount();
            } else {
                $notActive[] = $cart_item;
            }
        }
        return [
            'cart'=> $cart,
            'sum' => $sum,
            'notActive' => $notActive
        ];
    }
}