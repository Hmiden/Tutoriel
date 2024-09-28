<?php

// src/Form/TutorielType.php
// src/Form/TutorielType.php

namespace App\Form;

use App\Entity\Tutoriel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class TutorielType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titre', TextType::class, [
                'label' => 'Titre',
                'attr' => ['placeholder' => 'Entrez le titre du tutoriel']
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => ['placeholder' => 'Entrez la description du tutoriel']
            ])
            ->add('format', TextType::class, [
                'label' => 'Format',
                'attr' => ['placeholder' => 'Entrez le format du tutoriel']
            ])
            ->add('categorie', NumberType::class, [
                'label' => 'Catégorie',
                'attr' => ['placeholder' => 'Entrez la catégorie']
            ])
            ->add('duree', NumberType::class, [
                'label' => 'Durée (minutes)',
                'attr' => ['placeholder' => 'Entrez la durée en minutes']
            ])
            ->add('langue', TextType::class, [
                'label' => 'Langue',
                'attr' => ['placeholder' => 'Entrez la langue du tutoriel']
            ])
            ->add('filePath', FileType::class, [
                'label' => 'Fichier (Image/Video/PDF)',
                'mapped' => false, // Important to add this
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Tutoriel::class,
        ]);
    }
}
