<?php

namespace VinilShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Manufacturer
 *
 * @ORM\Table(name="manufacturer")
 * @ORM\Entity(repositoryClass="VinilShopBundle\Repository\ManufacturerRepository")
 */
class Manufacturer
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\OneToMany(targetEntity="VinilShopBundle\Entity\Product", mappedBy="manufacturer")
     */
    private $products;

    /**
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     *@Assert\File(maxSize = "7024k", mimeTypes={ "image/jpeg" , "image/png" })
     */
    private $titleImage;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->products = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Manufacturer
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getTitleImage()
    {
        return $this->titleImage;
    }

    /**
     * @param mixed $titleImage
     */
    public function setTitleImage($titleImage)
    {
        $this->titleImage = $titleImage;
    }

    /**
     * Add product
     *
     * @param \VinilShopBundle\Entity\Product $product
     *
     * @return Manufacturer
     */
    public function addProduct(\VinilShopBundle\Entity\Product $product)
    {
        $this->products[] = $product;

        return $this;
    }

    /**
     * Remove product
     *
     * @param \VinilShopBundle\Entity\Product $product
     */
    public function removeProduct(\VinilShopBundle\Entity\Product $product)
    {
        $this->products->removeElement($product);
    }

    /**
     * Get products
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProducts()
    {
        return $this->products;
    }
}
