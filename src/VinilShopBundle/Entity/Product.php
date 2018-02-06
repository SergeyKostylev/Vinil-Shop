<?php

namespace VinilShopBundle\Entity;

use Doctrine\Common\Annotations\Annotation\Required;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * product
 *
 * @ORM\Table(name="product")
 * @ORM\Entity(repositoryClass="VinilShopBundle\Repository\ProductRepository")
 */
class Product
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var Category
     *
     * @ORM\ManyToOne(targetEntity="VinilShopBundle\Entity\Category", inversedBy="products")
     */
    private $category;

    /**
     * @var Manufacturer
     *
     * @ORM\ManyToOne(targetEntity="VinilShopBundle\Entity\Manufacturer", inversedBy="products")
     */
    private $manufacturer;

    /**
     * @var string
     *
     * @ORM\Column(name="price", type="decimal", precision=7, scale=2)
     */
    private $price;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @Assert\File(mimeTypes={ "image/jpeg" })
     *
     */
    private $titleImage;

    /**
     * @var string
     *
     * @ORM\Column(name="other_images", type="string", length=255, nullable=true)
     */
    private $otherImages;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;

    /**
     * @var attribute[]
     *
     * @ORM\ManyToMany(targetEntity="VinilShopBundle\Entity\Attribute", inversedBy="products", cascade={"persist"})
     */
    private $attributes;




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
     * @return attribute[]
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @param attribute[] $attributes
     */
    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * @return Manufacturer
     */
    public function getManufacturer()
    {
        return $this->manufacturer;
    }

    /**
     * @param Manufacturer $manufacturer
     */
    public function setManufacturer($manufacturer)
    {
        $this->manufacturer = $manufacturer;
    }


    /**
     * Set name
     *
     * @param string $name
     *
     * @return product
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
     * Set category
     *
     * @param integer $category
     *
     * @return product
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return int
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set manufacturerId
     *
     * @param integer $manufacturerId
     *
     * @return product
     */
    public function setManufacturerId($manufacturerId)
    {
        $this->manufacturerId = $manufacturerId;

        return $this;
    }

    /**
     * Get manufacturerId
     *
     * @return int
     */
    public function getManufacturerId()
    {
        return $this->manufacturerId;
    }

    /**
     * Set price
     *
     * @param string $price
     *
     * @return product
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return string
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return product
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set titleImage
     *
     * @param string $titleImage
     *
     * @return product
     */
    public function setTitleImage($titleImage)
    {
        $this->titleImage = $titleImage;

        return $this;
    }

    /**
     * Get titleImage
     *
     * @return string
     */
    public function getTitleImage()
    {
        return $this->titleImage;
    }

    /**
     * Set otherImages
     *
     * @param string $otherImages
     *
     * @return product
     */
    public function setOtherImages($otherImages)
    {
        $this->otherImages = $otherImages;

        return $this;
    }

    /**
     * Get otherImages
     *
     * @return string
     */
    public function getOtherImages()
    {
        return $this->otherImages;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return product
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return bool
     */
    public function getIsActive()
    {
        return $this->isActive;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->attributes = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add attribute
     *
     * @param \VinilShopBundle\Entity\Attribute $attribute
     *
     * @return Product
     */
    public function addAttribute(\VinilShopBundle\Entity\Attribute $attribute)
    {
        $this->attributes[] = $attribute;

        return $this;
    }

    /**
     * Remove attribute
     *
     * @param \VinilShopBundle\Entity\Attribute $attribute
     */
    public function removeAttribute(\VinilShopBundle\Entity\Attribute $attribute)
    {
        $this->attributes->removeElement($attribute);
    }
}
