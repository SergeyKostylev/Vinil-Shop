<?php

namespace VinilShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * advertising_slider
 *
 * @ORM\Table(name="advertising_slider")
 * @ORM\Entity(repositoryClass="VinilShopBundle\Repository\Advertising_sliderRepository")
 */
class Advertising_slider
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
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     *@Assert\File(maxSize = "7024k", mimeTypes={ "image/jpeg" , "image/png" })
     */
    private $image;


    /**
     * @var File[]
     * @Assert\All(@Assert\File(maxSize = "7024k", mimeTypes={ "image/jpeg" , "image/png" }))
     */
    private $images;


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
     * Set image
     *
     * @param string $image
     *
     * @return advertising_slider
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @return File[]
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * @param File[] $images
     */
    public function setImages($images)
    {
        $this->images = $images;
    }


}

