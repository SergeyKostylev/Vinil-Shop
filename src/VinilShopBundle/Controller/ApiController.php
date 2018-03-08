<?php

namespace VinilShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use VinilShopBundle\Entity\Cart;
use VinilShopBundle\Entity\Feedback;
use VinilShopBundle\Entity\Product;
use VinilShopBundle\Entity\User;
use VinilShopBundle\Service\PriceSumInCart;


class ApiController extends Controller
{
    /**
     * @Route("/feedback/create", name="feedback_create")
     *
     */

    public function createFeedbackAction(Request $request)
    {

        $email= $request->get('email');
        $sender_name = $request->get('name');
        $title = $request->get('title');
        $message = $request->get('message');
        $captcha = (int)$request->get('captcha');

        if ($captcha != $this->get('session')->get('feedback_captcha')){
            return  new JsonResponse([
                'answer' => 'Неверное контрольное число',
                'itemBlock' => 'captcha'
            ],403);
        }

        if (!preg_match('/^[-._a-z0-9]+@(?:[a-z0-9][-a-z0-9]+\.)+[a-z]{2,6}$/',$email)){

            return  new JsonResponse([
                'answer' => 'Некорректный email',
                'itemBlock' => 'sender-email'
            ],403);
        }
        if(!preg_match('/^[А-ЯA-Z]{1}[a-zа-я]{1,50}$/u',$sender_name)){

            return  new JsonResponse([
                'answer' => 'Некорректное имя',
                'itemBlock' => 'sender-name'
            ],403);
        }
        if(strlen($title) < 4){

            return  new JsonResponse([
                'answer' => 'Короткое название темы',
                'itemBlock' => 'sender-message-title'
            ],403);
        }

        if(strlen($title) > 250){

            return  new JsonResponse([
                'answer' => 'Длинное название темы',
                'itemBlock' => 'sender-message-title'
            ],403);
        }

        if(strlen($message) < 50){

            return  new JsonResponse([
                'answer' => 'Маленькое сообщение',
                'itemBlock' => 'sender-message'
            ],403);
        }

        if(strlen($message) > 10000){

            return  new JsonResponse([
                'answer' => 'Длинное сообщение',
                'itemBlock' => 'sender-message'
            ],403);
        }

        $feedback = new Feedback();
        $feedback
            ->setEmail($email)
            ->setName($sender_name)
            ->setTitle($title)
            ->setMessage($message);

        $em = $this->getDoctrine()->getManager();
        $em->persist($feedback);
        $em->flush();

        return  new JsonResponse([
            'answer' => 'Сообщение отправлено',
        ],200);

    }

    /**
     * @Route("/cart/add/product/{product_id}", name="cart_user_add")
     *
     */

    public function addToUserCart(Request $request, $product_id)
    {
        $product = $this
            ->getDoctrine()
            ->getRepository('VinilShopBundle:Product')
            ->find($product_id);

        if(!$product){
            return  new JsonResponse([
                'answer' => 'Товар не найден'
            ],403);
        }

        /**
         * @var User $user
         */
        $user = $this->getUser();

        if(!$user){
            $session = new Session();
//            $session->remove('cart');die;
            if ($session->has('cart')){

                $cart = $session->get('cart');
                if(!array_key_exists($product_id, $cart))
                {
                    $cart[$product_id] = 1;
                    $session->set('cart', $cart);
                }

            }else{

                $cart =[$product_id => 1];
                $session->set('cart', $cart);
            }
//            dump($cart);
            return  new JsonResponse([
                'answer' => 'Товар добавлен в корзину'
            ],200);
        }

        $cart =  $this
            ->getDoctrine()
            ->getRepository('VinilShopBundle:Cart')
            ->findBy([
                'user' => $user->getId(),
                'product' => $product->getId()
            ]);
        if ($cart){
            return  new JsonResponse([
                'answer' => 'Товар уже в корзине'
            ],200);
        }

        $cart = new Cart();
        $cart
            ->setUser($user)
            ->setProduct($product);

        $em = $this->getDoctrine()->getManager();
        $em->persist($cart);
        $em->flush();

        return  new JsonResponse([
            'answer' => 'Добалнено в корзину',
        ],200);
    }


    /**
     * @Route("/cart/delete/user/product/{id}", name="cart_delete_user")
     *
     */

    public function cartDeleteUserProductAction(Request $request, $id)
    {
        $user= $this->getUser();

        if(!$user){
            return  new JsonResponse([
                'answer' => 'Пользователь отсутствует'
            ],403);
        }
        $cart = $this
            ->getDoctrine()
            ->getRepository('VinilShopBundle:Cart')
            ->findBy([
                    'product' => $id,
                    'user' => $user->getId()
                ]
            );
        if(!$cart){
            return  new JsonResponse([
                'answer' => 'Товар в корзине не найден'
            ],403);
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($cart[0]);
        $em->flush();

        $products_in_cart = $this
            ->getDoctrine()
            ->getRepository('VinilShopBundle:Cart')
            ->findBy([
                'user' => $user->getId()
            ]);
        $sum = PriceSumInCart::getSumInCarts($products_in_cart);


        return  new JsonResponse([
            'answer' => 'Товар удален из корзины',
            'sum' => $sum
        ],200);
    }



    /**
     * @Route("/cart/delete/anon/product/{id}", name="cart_delete_anon")
     *
     */

    public function cartDeleteAnonProductAction(Request $request, $id)
    {
        $session = new Session();
        if($session->has('cart')){

            $session_cart = $session->get('cart');
            unset($session_cart[$id]);
            $session->set('cart',$session_cart);

            $sum = 0;
            foreach ($session_cart as $product_id => $amount){
                $product = $this
                    ->getDoctrine()
                    ->getRepository('VinilShopBundle:Product')
                    ->find($product_id );
                if($product->getIsActive()){
                $sum+= $product->getPrice() * $amount;
                }

            }
            return  new JsonResponse([
                'answer' => 'Товар уделен из корзиный',
                'sum' => $sum
            ],200);
        }

        return  new JsonResponse([
            'answer' => 'Корзина отсутствует'
        ],200);
    }


    /**
     * @Route("/cart/set-amount/user/product/{id}/{act}", name="cart_up_amount_user")
     *
     */

    public function setCartAmountUserAction(Request $request, $id, $act = 1)
    {
        $user= $this->getUser();

        if(!$user){
            return  new JsonResponse([
                'answer' => 'Пользователь отсутствует'
            ],403);
        }
        $cart = $this
            ->getDoctrine()
            ->getRepository('VinilShopBundle:Cart')
            ->findBy([
                    'product' => $id,
                    'user' => $user->getId()
                ]
            );
        if(!$cart){
            return  new JsonResponse([
                'answer' => 'Товар в корзине не найден'
            ],403);
        }

        $cart = $cart[0];
        $amount = $cart->getAmount();
        if($act == 1){
            $cart->setAmount(++$amount);
        }
        if($act == -1 && $amount > 1 ){
        $cart->setAmount(--$amount);
        }
        $em = $this->getDoctrine()->getManager();
        $em->persist($cart);
        $em->flush();

        $products_in_cart = $this
            ->getDoctrine()
            ->getRepository('VinilShopBundle:Cart')
            ->findBy([
                'user' => $user->getId()
                ]);
        $sum = PriceSumInCart::getSumInCarts($products_in_cart);

        return  new JsonResponse([
            'amount' => $cart->getAmount(),
            'sum' => $sum
        ],200);
    }

    /**
     * @Route("/cart/set-amount/anon/product/{id}/{act}", name="cart_up_amount_anon")
     *
     */

    public function setCartAmountAnonAction(Request $request,$id, $act = 1)
    {
        $session = new Session();
        if($session->has('cart')){

            $session_cart = $session->get('cart');

            if(isset($session_cart[$id]))
            {
                $amount = $session_cart[$id];
                if($act == 1){
                    $session_cart[$id] ++;
                }
                if($act == -1 && $amount > 1 ){
                    $session_cart[$id] --;
                }
            }
            $session->set('cart',$session_cart);
            $sum = 0;
            foreach ($session_cart as $product_id => $amount){
                $product = $this
                    ->getDoctrine()
                    ->getRepository('VinilShopBundle:Product')
                    ->find($product_id );
                if($product->getIsActive()){
                    $sum+= $product->getPrice() * $amount;
                }
            }
            return  new JsonResponse([
                'answer' => 'Товар уделен из корзиный',
                'sum' => $sum,
                'amount' => $session_cart[$id]
            ],200);
        }

        return  new JsonResponse([
            'answer' => 'Корзина отсутствует'
        ],200);
    }

    /**
     * @Route("/amount-product-in-cart", name="one")
     *
     */

    public function oneTwoAction()
    {
        $amount = 0;
        $user = $this->getUser();
        if($user)
        {
            $carts = $this
                ->getDoctrine()
                ->getRepository('VinilShopBundle:Cart')
                ->findBy([
                    'user' => $user->getId()
                ]);

            foreach ($carts as $cart){
                $amount+= $cart->getAmount();
            }
            return  new JsonResponse([
                'amount' => $amount
            ],200);
        }

        $session = new Session();
        if($session->has('cart')){
            $cart = $session->get('cart');
            foreach ($cart as $product_id => $product_count){
                $amount+= $product_count;
            }
            return  new JsonResponse([
                'amount' => $amount
            ],200);
        }

        return  new JsonResponse([
            'amount' => $amount
        ],200);

    }


}
