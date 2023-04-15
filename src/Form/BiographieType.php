<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BiographieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $listemot = [
            'GG' => 'GG',
            'Vainqueur' => 'Vainqueur',
            'Roi des MMIs' => 'Roi des MMIs',
            'dev' => 'dev > crea',
            'crea' => 'crea > dev',
        ];

        $builder
            ->add('biographie', ChoiceType::class, [
                'choices' => $listemot,
                'required' => true,
                'label' => false,
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
