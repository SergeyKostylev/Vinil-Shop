<?php

namespace VinilShopBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ManufacturerType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $required_file = $options['required_file'];
        $builder
            ->add('name', null,['label'=>'Создать нового производителя'])
            ->add('titleImage',FileType::class,
                [
                    'label' => 'Логотип',
                    'data_class' => null,
                    'required' => $required_file
                ]);
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'VinilShopBundle\Entity\Manufacturer',
            'required_file' => false
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'vinilshopbundle_manufacturer';
    }


}
