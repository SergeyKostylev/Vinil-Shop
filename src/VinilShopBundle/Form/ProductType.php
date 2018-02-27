<?php

namespace VinilShopBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use VinilShopBundle\Entity\Category;
use VinilShopBundle\Repository\CategoryRepository;
use VinilShopBundle\Repository\ManufacturerRepository;


class ProductType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $required_file = $options['required_file'];

        $builder
            ->add('name')
            ->add('price')
            ->add('description')
            ->add('titleImage',FileType::class,
                [
                    'label' => 'Титульное изображение',
                    'data_class' => null,
                    'required' => $required_file

                ])
            ->add('otherImages', FileType::class,
                [
                    'label' => 'Изображения для галереи',
                    'multiple' => true,
                    'required' => false
                ])
//            ->add('galleryImages',EntityType::class,)
            ->add('isActive')
            ->add('category', EntityType::class, ['choice_label'=>'name',
                        'class'=> Category::class,
                        'placeholder' => 'Выберите категорию',
                        'query_builder'=> function(CategoryRepository $repository) {
                        return
                        $repository
                            ->createQueryBuilder('c')
                            ->Where('c.lastCategory = true');
                        }
            ])
            ->add('manufacturer', null, [
                'choice_label'=>'name',
                'label' => false,
                'placeholder' => 'Выберите производителя',
                'required' => true,
                'query_builder'=> function(ManufacturerRepository $repository) {
                    return
                        $repository
                            ->createQueryBuilder('manuf')
                            ->orderBy('manuf.name', 'ASC');
                }

            ]);

    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'VinilShopBundle\Entity\Product',
            'required_file' => false
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'vinilshopbundle_product';
    }


}
