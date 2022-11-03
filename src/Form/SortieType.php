<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Sortie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label'=>'Titre : '
            ])
            ->add('dateHeureDebut', DateType::class, [
                'label'=>'Date de début : ',
                'widget'=>'single_text'
            ])
            ->add('duree', NumberType::class, [
                'label'=>'Durée de l\'activité : '
            ])
            ->add('dateLimiteInscription', DateType::class, [
                'label'=>'Date Limite d\'inscription : ',
                'widget'=>'single_text'
            ])

            ->add('nbInscriptionsMax', NumberType::class, [
                'label'=>'Nombre de participants MAX : '
            ])
            ->add('infosSortie', TextareaType::class, [
                    'label'=>'Description : '
            ])

            ->add('etat', EntityType::class, [
                'class'=>Etat::class,
                'choice_label'=>'libelle',
                'label'=>'Etat : '
            ])
            ->add('lieu', EntityType::class, [
                'class'=>Lieu::class,
                'choice_label'=>'nom',
                'label'=>'Lieu : '
            ])
             ->add('campus', EntityType::class, [
                 'class'=>Campus::class,
                 'choice_label'=>'nom',
                 'label'=>'Campus : '
             ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
