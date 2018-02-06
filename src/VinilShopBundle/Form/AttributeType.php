<?php

namespace VinilShopBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use VinilShopBundle\Entity\Attribute_name;
use VinilShopBundle\Repository\Attribute_nameRepository;
use VinilShopBundle\Repository\AttributeRepository;

class AttributeType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $category = $options['category_id'];

        $builder
            ->add('name' , EntityType::class,[
                'choice_label' => 'name',
                'class' => Attribute_name::class,
                'label' => 'Характеристика',
                'placeholder' =>'Не выбрано',
                'query_builder' => function(Attribute_nameRepository $attribute) use ($category) {

                        $qb = $attribute->createQueryBuilder('a');
                        $qb->join('a.categoryes','c');

                        if ($category){
                            $qb
                            ->where('c = :id')
                            ->setParameter('id', $category);
                        }

                    return $qb;
                }
            ])
            ->add('value')
//            ->add('products')
        ;
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'VinilShopBundle\Entity\Attribute',
            'category_id' => false
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'vinilshopbundle_attribute';
    }


}
