<?php

namespace App\Form;

use App\Entity\Participant;
use App\Entity\Site;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreationProfilType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('administrateur',ChoiceType::class,[
                'expanded' => true,
                'choices' => [
                    'Administrateur' => true,
                    'Utilisateur' => false,
                ],
                'label' => 'Profil : ',
            ])
            ->add('nom',TextType::class,[
                'label' => 'Nom : ',
            ])
            ->add('prenom',TextType::class,[
                'label' => 'Prenom : ',
            ])
            ->add('pseudo',TextType::class,[
                'label' => 'Pseudo : ',
            ])
            ->add('telephone',TextType::class,[
                'label' => 'Telephone : ',
            ])
            ->add('email',TextType::class,[
                'label' => 'Email : ',
            ])
            ->add('password',TextType::class,[
                'mapped' => false,
                'label' => 'Mot De Passe : ',
                'attr' => ['autocomplete' => 'new-password'],
            ])
            ->add('confirmationPassword',TextType::class,[
                'mapped' => false,
                'label' => 'Confirmation : ',
            ])
            ->add('site',EntityType::class,[
                'class' => Site::class,
                'choice_label' => 'nom',
                'label' => 'Site de rattachement : ',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }
}
