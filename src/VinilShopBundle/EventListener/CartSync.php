<?php
namespace VinilShopBundle\EventListener;

use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use VinilShopBundle\Entity\Cart;
use VinilShopBundle\Entity\User;

class CartSync
{
    protected $container;

    public function __construct(ContainerInterface $container) // this is @service_container
    {
        $this->container = $container;
    }

    public function syncAction()
    {
        $session = new Session();
        $cart_in_session = $session->get('cart');
        if ($cart_in_session) {
            /**
             * @var User $user
             */
            $user = $this->container->get('security.token_storage')->getToken()->getUser();

            $em = $this
                ->container
                ->get('doctrine')
                ->getManager();

            $user_cart = $this
                ->container
                ->get('doctrine')
                ->getRepository('VinilShopBundle:Cart')
                ->findBy([
                    'user' => $user->getId()
                ]);

            foreach ($cart_in_session as $product_id => $amount_in_session_cart) {
                /**
                 * @var Cart $cart
                 */
                foreach ($user_cart as $cart) {
                    if ($cart->getProduct()->getId() == $product_id && $cart->getAmount() < $amount_in_session_cart) {
                        $cart->setAmount($amount_in_session_cart);

                        $em->persist($cart);
                        $em->flush();
                        unset($cart_in_session[$product_id]);
                    }
                }
                $update_cart = $this
                    ->container
                    ->get('doctrine')
                    ->getRepository('VinilShopBundle:Cart')
                    ->findBy([
                        'user' => $user->getId(),
                        'product' => $product_id
                    ]);
                if (!$update_cart) {
                    $product = $this
                        ->container
                        ->get('doctrine')
                        ->getRepository('VinilShopBundle:Product')
                        ->find($product_id);
                    if ($product) {
                        $cart = new Cart();
                        $cart->setProduct($product);
                        $cart->setUser($user);
                        $cart->setAmount($amount_in_session_cart);
                        $em->persist($cart);
                        $em->flush();
                        $cart_in_session[$product_id] = 0;
                    }
                }

            }
            $session->remove('cart');
        }
    }
}