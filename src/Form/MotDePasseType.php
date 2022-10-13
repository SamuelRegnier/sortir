<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MotDePasseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Password',TextType::class,[
                'mapped' => false,
                'label' => 'Mot De Passe actuel : ',
                'attr' => [ 'placeholder' => 'Mot de Passe actuel'
                ],
            ])
            ->add('NouveauPassword',TextType::class,[
                'mapped' => false,
                'label' => 'Nouveau Mot De Passe : ',
                'attr' => [ 'placeholder' => 'Nouveau Mot de Passe'
                ],
            ])
            ->add('ConfirmationPassword',TextType::class,[
                'mapped' => false,
                'label' => 'Confirmation Mot De Passe : ',
                'attr' => [ 'placeholder' => 'Confirmation Nouveau Mot de Passe'
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
