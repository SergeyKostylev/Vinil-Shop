<?php

namespace VinilShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * product
 *
 * @ORM\Table(name="product")
 * @ORM\Entity(repositoryClass="VinilShopBundle\Repository\productRepository")
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
     * @var int
     *
     * @ORM\Column(name="article", type="integer")
     */
    private $article;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var int
     *
     * @ORM\Column(name="category_id", type="integer")
     */
    private $categoryId;

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
     * @var string
     *
     * @ORM\Column(name="title_image", type="string", length=255, nullable=true)
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
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set article
     *
     * @param integer $article
     *
     * @return product
     */
    public function setArticle($article)
    {
        $this->article = $article;

        return $this;
    }

    /**
     * Get article
     *
     * @return int
     */
    public function getArticle()
    {
        return $this->article;
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
     * Set categoryId
     *
     * @param integer $categoryId
     *
     * @return product
     */
    public function setCategoryId($categoryId)
    {
        $this->categoryId = $categoryId;

        return $this;
    }

    /**
     * Get categoryId
     *
     * @return int
     */
    public function getCategoryId()
    {
        return $this->categoryId;
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
}

