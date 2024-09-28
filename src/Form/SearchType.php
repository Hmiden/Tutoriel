<?php
// src/Form/SearchType.php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('titre', TextType::class, [
            'required' => false,
            'label' => 'Titre',
            'attr' => [
                'placeholder' => 'Rechercher par titre',
            ],
        ])
            ->add('format', ChoiceType::class, [
                'required' => false,
                'choices' => [
                    'Tous' => '',
                    'Image' => '.jpg',
                    'Video' => '.mp4',
                    'PDF' => '.pdf',
                ],
                'label' => 'Format'
            ])
            ->add('duree', ChoiceType::class, [
                'required' => false,
                'choices' => [
                    'Tous' => '',
                    'moins 30min' => 30,
                    'plus 30min' => 61,
                ],
                'label' => 'Duration'
            ])
            ->add('langue', ChoiceType::class, [
                'required' => false,
                'choices' => [
                    'Tous' => '',
                    'English' => 'anglais',
                    'French' => 'francais',
                    'arabe' => 'arabe',
                ],
                'label' => 'Language'
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Recherche',
                'attr' => ['class' => 'btn btn-primary']
            ]);
    }
    

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
    
}


