<?php

namespace VinilShopBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use VinilShopBundle\Entity\Category;
use VinilShopBundle\Repository\CategoryRepository;



class ProductType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('price')
            ->add('description')
            ->add('titleImage',FileType::class,
                [
                    'label' => 'Титульное изображение',
                    'data_class' => null,
//                    'property_path' => 'brochure',
                    'required' => false
                ])
            ->add('otherImages')
            ->add('isActive')
            ->add('category', EntityType::class, ['choice_label'=>'name',
                        'class'=> Category::class,
                        'query_builder'=> function(CategoryRepository $repository) {
                        return
                        $repository
                            ->createQueryBuilder('c')
                            ->Where('c.lastCategory = true');
                        }
            ])
            ->add('manufacturer', null, ['choice_label'=>'name','label' => false]);

    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'VinilShopBundle\Entity\Product'
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
