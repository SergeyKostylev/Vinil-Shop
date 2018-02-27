<?php

namespace VinilShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Category
 *
 * @ORM\Table(name="category")
 * @ORM\Entity(repositoryClass="VinilShopBundle\Repository\CategoryRepository")
 */
class Category
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
     * @ORM\OneToMany(targetEntity="Category", mappedBy="parent")
     */
    protected $children;

    /**
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="children")
     */
    private $parent;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     *@Assert\File(maxSize = "7024k", mimeTypes={ "image/jpeg" , "image/png" })
     */
    private $titleImage;

    /**
     * @var bool
     *
     * @ORM\Column(name="last_category", type="boolean")
     */
    private $lastCategory;

    /**
     * @var string
     *
     * @ORM\OneToMany(targetEntity="VinilShopBundle\Entity\Product", mappedBy="category")
     */
    private $products;

    /**
     * @var Attribute_name[]
     *
     * @ORM\ManyToMany(targetEntity="VinilShopBundle\Entity\Attribute_name", inversedBy="categoryes")
     */
    private $attribute_names;

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
     * @return string
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * @param string $products
     */
    public function setProducts($products)
    {
        $this->products = $products;
    }

    /**
     * @return Attribute_name[]
     */
    public function getAttributeNames()
    {
        return $this->attribute_names;
    }

    /**
     * @param Attribute_name[] $attribute_names
     */
    public function setAttributeNames($attribute_names)
    {
        $this->attribute_names = $attribute_names;
    }




    /**
     * @return bool
     */
    public function isLastCategory()
    {
        return $this->lastCategory;
    }

    /**
     * @param bool $lastCategory
     */
    public function setLastCategory($lastCategory)
    {
        $this->lastCategory = $lastCategory;
    }




    public function __construct()
    {
        $this->children = new ArrayCollection();
    }


    public function getParent() {
        return $this->parent;
    }

    public function getChildren() {
        return $this->children;
    }

    public function addChild(Category $child) {
        $this->children[] = $child;
        $child->setParent($this);
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
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Set name
     *
     * @param string $parent
     *
     * @return Category
     */
    public function setParent($parent)
    {
        $this->parent = $parent;

        return $this;
    }


    /**
     * Get lastCategory
     *
     * @return boolean
     */
    public function getLastCategory()
    {
        return $this->lastCategory;
    }

    /**
     * Remove child
     *
     * @param \VinilShopBundle\Entity\Category $child
     */
    public function removeChild(\VinilShopBundle\Entity\Category $child)
    {
        $this->children->removeElement($child);
    }

    /**
     * Add product
     *
     * @param \VinilShopBundle\Entity\Product $product
     *
     * @return Category
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
     * Add attributeName
     *
     * @param \VinilShopBundle\Entity\Attribute_name $attributeName
     *
     * @return Category
     */
    public function addAttributeName(\VinilShopBundle\Entity\Attribute_name $attributeName)
    {
        $this->attribute_names[] = $attributeName;

        return $this;
    }

    /**
     * Remove attributeName
     *
     * @param \VinilShopBundle\Entity\Attribute_name $attributeName
     */
    public function removeAttributeName(\VinilShopBundle\Entity\Attribute_name $attributeName)
    {
        $this->attribute_names->removeElement($attributeName);
    }
}
