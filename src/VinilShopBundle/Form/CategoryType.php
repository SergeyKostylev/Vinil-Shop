<?php

namespace VinilShopBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use VinilShopBundle\Entity\Attribute_name;
use VinilShopBundle\Entity\Category;
use VinilShopBundle\Repository\CategoryRepository;

use VinilShopBundle\Repository\Attribute_nameRepository;

class CategoryType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $required_file = $options['required_file'];
        $builder
            ->add('parent',EntityType::class,[
                'choice_label'=>'name',
                'required' => false,
                'class'=> Category::class,
                'query_builder'=> function(CategoryRepository $repository){
                    return
                        $repository
                        ->createQueryBuilder('c')
                        ->Where('c.lastCategory = false');
                }])
            ->add('name')
            ->add('attributeNames',EntityType::class,[
                'choice_label'=>'name',
                'required' => false,
                'multiple' =>true,
                'expanded' => true,
                'label' => false,
                'class'=> Attribute_name::class,
                'query_builder' => function (Attribute_nameRepository $er) {
                    return $er->createQueryBuilder('att')
                        ->orderBy('att.name', 'ASC');
                },
            ])
            ->add('lastCategory')
            ->add('titleImage',FileType::class,
                [
                    'label' => 'Титульное изображение',
                    'data_class' => null,
                    'required' => $required_file

                ])
            ;
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'VinilShopBundle\Entity\Category',
            'required_file' => false
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'vinilshopbundle_category';
    }
}