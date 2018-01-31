<?php

namespace VinilShopBundle\Form;


use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use VinilShopBundle\Entity\Attribute_name;
use VinilShopBundle\Entity\Category;
use VinilShopBundle\Repository\CategoryRepository;


class CategoryType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
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
                'class'=> Attribute_name::class])
            ->add('lastCategory')
            ;
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'VinilShopBundle\Entity\Category'
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
