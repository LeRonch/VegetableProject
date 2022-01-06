<?php

namespace App\Form;

use App\Entity\Client;
use App\Entity\Product;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\OptionsResolver\OptionsResolver;


class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('product_name')
            ->add('price', NumberType::class, array (
                'required' => true,
                'scale' => 2,
                'attr' => array(
                   'min' => -90,
                   'max' => 90,
                ),
            ))
            ->add('quantity')
            ->add('type' , ChoiceType::class, [
                'choices'  => [
                    'Fruit' => false,
                    'Légume' => true,
                ],
                ])
            ->add('image')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
