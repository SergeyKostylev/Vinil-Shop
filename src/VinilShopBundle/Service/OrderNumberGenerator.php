<?php

namespace VinilShopBundle\Service;


abstract class OrderNumberGenerator
{


    public static function getOrderNumber()
    {
        $number = (substr('ABCDEFGHIJKLMNOPQRSTUVWXYZ',rand(0,25),3)) . time();

        return $number;
    }

}