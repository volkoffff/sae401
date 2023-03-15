<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options, ): void
    {
        $avatarOptions = [
            'avatar1.png',
            'avatar2.png',
            'avatar3.png',
            'avatar4.png',
            'avatar5.png',
            'avatar6.png',
            'avatar7.png',
            'avatar8.png',
            'avatar9.png',
        ];
        $listecouleur = [
            '#F34B4B',
            '#4BF352',
            '#4B70F3',
        ];

        $builder
            ->add('email')
            ->add('pseudo')
            ->add('password', PasswordType::class)
            ->add('confirm_password', PasswordType::class)
            ->add('avatar', ChoiceType::class, [
                'choices' => $avatarOptions,
                'required' => true,
                'expanded' => true,
                'multiple' => false,
                'label' => false,
                'choice_label' => false,

            ])

            ->add('couleur', ChoiceType::class, [
                'choices' => $listecouleur,
                'required' => true,
                'expanded' => true,
                'multiple' => false,]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}