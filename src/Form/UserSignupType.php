<?php
namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class UserSignupType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'attr' => ['placeholder' => 'Entrez votre nom'],
            ])
            ->add('prenom', TextType::class, [
                'attr' => ['placeholder' => 'Entrez votre prénom'],
            ])
            ->add('email', EmailType::class, [
                'attr' => ['placeholder' => 'Entrez votre email'],
           
            ])
            ->add('password', PasswordType::class, [
                'attr' => ['placeholder' => 'Entrez votre mot de passe'],
                'label' => 'Mot de passe',
            ])
            ->add('role', ChoiceType::class, [
                'choices' => [
                    'User' => ['ROLE_USER'],
                    
                ],
                'expanded' => true,
                'multiple' => false,
                'label' => 'Rôle',
            ]);
        
           
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
