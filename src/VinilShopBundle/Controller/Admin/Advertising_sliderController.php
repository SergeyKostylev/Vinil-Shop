<?php

namespace VinilShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use VinilShopBundle\Entity\Advertising_slider;
use VinilShopBundle\Form\Advertising_sliderType;
use VinilShopBundle\Service\FileUploader;

class Advertising_sliderController extends Controller
{
    /**
     * @Route("/admin/advertising-slider", name="admin_advertising_slider")
     * @Template()
     */

    public function indexAction(Request $request)
    {
        $slider = new Advertising_slider();
        $form = $this->createForm(Advertising_sliderType::class,$slider);
        $form->add('submit',SubmitType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $images = $slider->getImages();
            /**
             * @var UploadedFile $image
             */
            foreach ($images as $image) {
                $fileUploader = new FileUploader($this->getParameter('advertising_slider_images'));
                $fileName = $fileUploader->upload($image);
                $slider_image = new Advertising_slider();
                $slider_image->setImage($fileName);

                $em = $this->getDoctrine()->getManager();
                $em->persist($slider_image);
            }

            $em->flush();
            return $this->redirectToRoute('admin_default');
        }

        $advSlider = $this
            ->getDoctrine()
            ->getRepository('VinilShopBundle:Advertising_slider')
            ->findAll();

        return [
            'form' => $form->createView(),
            'slider' => $advSlider
        ];
    }
}
