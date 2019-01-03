<?php

namespace VinilShopBundle\Service;

use VinilShopBundle\Entity\Cart;

abstract class PriceSumInCart
{
    public static function getSumInCarts($carts)
    {
        $sum = 0;
        /**
         * @var Cart $cart
         */
        foreach ($carts as $cart) {
            if ($cart->getProduct()->getIsActive()) {
                $product_price = $cart->getProduct()->getPrice();
                $amount = $cart->getAmount();
                $sum+=  $product_price * $amount;
            }
        }
        return $sum;
    }
}