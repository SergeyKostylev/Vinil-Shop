<?php
namespace VinilShopBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use VinilShopBundle\Entity\Cart;
use VinilShopBundle\Entity\User;
use VinilShopBundle\Service\PriceSumInCart;


class OrderController extends Controller
{

    /**
     * @Route("/order/create", name="order_create")
     * @Template()
     */
    public function createAction(Request $request)
    {
        $cart=[];
        $order_sum = 0;
        /**
         * @var User $user
         */
        $user = $this->getUser();
        if ($user){
            $carts = $this
                ->getDoctrine()
                ->getRepository('VinilShopBundle:Cart')
                ->findBy([
                    'user' => $user->getId()
                    ]);

            foreach ($carts as $item)
                {
                    /**
                     * @var Cart $item
                     */
                    $product = $item->getProduct();
                    if($product->getIsActive()){
                        $cart[] =[
                            'product' => $product,
                            'amount' => $item->getAmount()
                        ];
                    }
                }
            if(!count($cart)){
                return $this->redirectToRoute('home_page');
            }
            $order_sum = PriceSumInCart::getSumInCarts($carts);

            return [
                'cart' => $cart,
                'order_sum' => $order_sum
            ];
        }else{
            $session = new Session();

            if($session->has('cart') && count($session->get('cart'))){
                $session_cart = $session->get('cart');
                foreach ($session_cart as $product_id => $amount){
                    $product = $this
                        ->getDoctrine()
                        ->getRepository('VinilShopBundle:Product')
                        ->find($product_id);
                    if($product->getIsActive()){
                        $cart[] =[
                            'product' => $product,
                            'amount' => $amount
                            ];
                        $order_sum+= $product->getPrice() * $amount;
                    }
                }
            }
            if(!count($cart)){
                return $this->redirectToRoute('home_page');
            }
            return [
                'cart' => $cart,
                'order_sum' => $order_sum
            ];
        }




    }

}