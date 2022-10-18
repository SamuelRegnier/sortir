<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Site;
use App\Entity\Sortie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class,[
                'label'=>'Nom de la sortie:',
                'constraints'=>[
                    new notBlank(),
                    new length(['max'=> 30])
                ]])
            ->add('dateHeureDebut', DateTimeType::class,['label'=>'Date et Heure de la sortie :',
                'constraints'=>[
                    new notBlank()
                ]])
            ->add('dateLimiteInscription', DateTimeType::class,['label'=>'Date limite d\'inscription :',
                'constraints'=>[
                    new notBlank()
                ]])
            ->add('nbInscriptionsMax', TextType::class, [
                'label'=>'Nombre de place :',
                'constraints'=>[
                    new notBlank()
                ]])
            ->add('duree', NumberType::class,['label'=>'Durée :'])
            ->add('infosSortie', TextType::class, ['label'=>'Descritpion Sortie :',
                'constraints'=>[
                    new notBlank(),
                ]])
            ->add('lieux', EntityType::class,[
                    'class'=>Lieu::class,
                    'choice_label'=>'nom',
                    'label'=>'Lieu :',
                'constraints'=>[
                    new notBlank()
                ]])
            ->add('selectionner',ChoiceType::class,[
                'mapped' => false,
                'expanded' => true,
                'choices' => [
                    'Enregistrer' => true,
                    'Publier' => false,
                ],
                'label' => 'Veuillez faire un choix : ',
            ])
    ;}

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
