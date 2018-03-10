<?php

namespace VinilShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * State
 *
 * @ORM\Table(name="product_in_orders")
 * @ORM\Entity(repositoryClass="VinilShopBundle\Repository\ProductInOrderRepository")
 */
class ProductInOrder
{
    /**
     * @var int
     *
     * @ORM\Column(name="amount", type="integer")
     */
    protected $amount;

    /**
     * @ORM\Id()
     *
     * @var Orders
     *
     * @ORM\ManyToOne(targetEntity="VinilShopBundle\Entity\Orders", inversedBy="orderProducts")
     * @ORM\JoinColumn(name="order_id", referencedColumnName="id", nullable=false)
     */
    protected $order;

    /**
     * @ORM\Id()
     *
     * @var Product
     *
     * @ORM\ManyToOne(targetEntity="VinilShopBundle\Entity\Product", inversedBy="orderProducts")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id", nullable=false)
     */
    protected $product;

    /**
     * @var integer
     *
     * @ORM\Column(name="price_for_one", type="integer")
     */
    private $priceForOne;

    /**
     * @return int
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param int $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * @return Orders
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param Orders $order
     */
    public function setOrder($order)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * @return Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @param Product $product
     */
    public function setProduct($product)
    {
        $this->product = $product;

        return $this;
    }

    /**
     * @return int
     */
    public function getPriceForOne()
    {
        return $this->priceForOne;
    }

    /**
     * @param int $priceForOne
     */
    public function setPriceForOne($priceForOne)
    {
        $this->priceForOne = $priceForOne;

        return $this;
    }





}

