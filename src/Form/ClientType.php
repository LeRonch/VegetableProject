<?php

namespace App\Form;

use App\Entity\Client;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType as TypeTextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('adress')
            ->add('country')
            ->add('phone', TypeTextType::class, [
                'attr' => ['maxlength' => 10]
            ])
            ->add('days', TextareaType::class)
            ->add('city')
            ->add('postal', TypeTextType::class, [
                'attr' => ['maxlength' => 5]
            ])
            ->add('statut' , ChoiceType::class, [
                'choices'  => [
                    'Particulier' => false,
                    'Professionel' => true,
                ],
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Client::class,
        ]);
    }
}
