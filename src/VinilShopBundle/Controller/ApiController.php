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
use VinilShopBundle\Entity\Orders;
use VinilShopBundle\Entity\Product;
use VinilShopBundle\Entity\ProductInOrder;
use VinilShopBundle\Entity\State;
use VinilShopBundle\Entity\User;
use VinilShopBundle\Service\OrderNumberGenerator;
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
     * @Route("/amount-product-in-cart", name="amount_product_in_cart")
     *
     */
    public function amountInCart()
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

    /**
     * @Route("/api/order/create", name="order_create_api")
     *
     */
    public function apiOrderCreateAction(Request $request)
    {

        $name = $request->get('name');
        $email = $request->get('email');
        $phone = $request->get('phone');
        $address = $request->get('address');
        $description = $request->get('description');

        if (!preg_match('/^[-._a-z0-9]+@(?:[a-z0-9][-a-z0-9]+\.)+[a-z]{2,6}$/',$email)){

            return  new JsonResponse([
                'answer' => 'Некорректный email.',
            ],403);
        }

        $em = $this
            ->getDoctrine()
            ->getManager();

        $user = $this->getUser();

        if($user){
            $carts = $this
                ->getDoctrine()
                ->getRepository('VinilShopBundle:Cart')
                ->findBy([
                        'user' => $user->getId()
                    ]);
            if($carts){
                $order = new Orders();
                $state = $this
                    ->getDoctrine()
                    ->getRepository('VinilShopBundle:State')
                    ->find(1);

                $number_order = OrderNumberGenerator::getOrderNumber();
                $order
                    ->setNumber($number_order)
                    ->setBuyerName($name)
                    ->setPhone($phone)
                    ->setAddress($address)
                    ->setEmail($email)
                    ->setUser($user)
                    ->setState($state)
                    ->setPrice(PriceSumInCart::getSumInCarts($carts))
                    ->setDescription($description)
                ;
                $em->persist($order);

                foreach ($carts as $cart)
                {
                    /**
                     * @var Cart $cart
                     */
                    if($cart->getProduct()->getIsActive()) {
                        $productInOrder = new ProductInOrder();
                        $productInOrder
                            ->setOrder($order)
                            ->setProduct($cart->getProduct())
                            ->setAmount($cart->getAmount())
                            ->setPriceForOne($cart->getProduct()->getPrice());
                        $em->persist($productInOrder);
                        $em->remove($cart);
                    }
                }
                $em->flush();
            }else{
                return  new JsonResponse([
                    'answer' => 'Корзина пуста'
                ],403);
            }
        }else{

            $session = new Session();
            if($session->has('cart')){

                $cart = $session->get('cart');
                if(count($cart)){
                     $product_to_order =[];
                     $order_price = 0;

                    foreach ($cart as $product_id => $amount){
                        /**
                         * @var Product $product
                         */
                        $product = $this
                            ->getDoctrine()
                            ->getRepository('VinilShopBundle:Product')
                            ->find($product_id);
                        if($product->getIsActive()){
                            $order_price+= $product->getPrice() * $amount;
                            $product_to_order[] = [
                                'product' => $product,
                                'amount' => $amount
                            ];
                        }
                    }
                    if (count($product_to_order)) {

                        $order = new Orders();
                        $state = $this
                            ->getDoctrine()
                            ->getRepository('VinilShopBundle:State')
                            ->find(1);
                        $number_order = OrderNumberGenerator::getOrderNumber();
                        $order
                            ->setNumber($number_order)
                            ->setBuyerName($name)
                            ->setPhone($phone)
                            ->setAddress($address)
                            ->setEmail($email)
                            ->setState($state)
                            ->setPrice($order_price)
                            ->setDescription($description);
                        $em->persist($order);

                        foreach ($product_to_order as $product){

                            $productInOrder = new ProductInOrder();
                            $productInOrder
                                ->setOrder($order)
                                ->setProduct($product['product'])
                                ->setAmount($product['amount'])
                                ->setPriceForOne($product['product']->getPrice());
                            $em->persist($productInOrder);
                            unset($cart[$product['product']->getId()]);
                        }
                    }
                    $em->flush();
                    $session->set('cart', $cart);
                }
            }

        }

                $message = \Swift_Message::newInstance()
            ->setFrom('s03540@ukr.net')
            ->setTo($email)
            ->setSubject('Покупка на Music shop')
            ->setBody('Спасибо что выбрали наш магазин. <br>
                                Наш манеджер свяжется с Вами в ближайшее время для подтверждения.<br>
                                Номер вашего заказа <b>' . $number_order . '</b>.','text/html')

            ;

            $this->get('mailer')
                ->send($message);

        return  new JsonResponse([
            'answer' => 'Заказ '. $number_order .' оформлен.',
            'number_order' => $number_order
        ],200);
    }

}





















