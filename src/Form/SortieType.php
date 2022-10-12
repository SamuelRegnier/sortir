<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Site;
use App\Entity\Sortie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class,['label'=>'Nom de la sortie:'])
            ->add('dateHeureDebut', DateTimeType::class,['label'=>'Date et Heure de la sortie :'])
            ->add('dateLimiteInscription', DateType::class,['label'=>'Date limite d\'inscription :'])
            ->add('nbInscriptionsMax', TextType::class,['label'=>'Nombre de place :'])
            ->add('duree', NumberType::class,['label'=>'Durée :'])
            ->add('infosSortie', TextType::class, ['label'=>'Descritpion Sortie :'])
            ->add('lieux', EntityType::class,[
                    'class'=>Lieu::class,
                    'choice_label'=>'nom',
                    'label'=>'Lieu :'

            ])

    ;}

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
