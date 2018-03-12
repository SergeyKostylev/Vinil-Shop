<?php

namespace VinilShopBundle\Repository;

/**
 * OrdersRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class OrdersRepository extends \Doctrine\ORM\EntityRepository
{
    public function serchForNumber($search)
    {
        return $this
            ->createQueryBuilder('ord')
            ->where('ord.number LIKE :number')
            ->setParameter('number', '%' . $search . '%')
            ->getQuery()
            ->getResult();
    }
}
