<?php

namespace App\Form;

use App\Form\Model\FiltresSortiesFormModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FiltresSortiesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('campus', ChoiceType::class, [
                'label' => 'Campus '
            ])
            ->add('nomRecherche', SearchType::class, [
                'label' => 'Le nom de la sortie contient : ',
            ])
            ->add('dateDebut', DateType::class, [
                'label' => 'Entre ',
                'widget' => 'single_text'
            ])
            ->add('dateFin', DateType::class, [
                'label' => 'et ',
                'widget' => 'single_text'
            ])
            ->add('isOrganisateur', CheckboxType::class, [
                'label' => 'Sorties dont je suis l\'organisateur.trice ',
            ])
            ->add('isInscrit', CheckboxType::class, [
                'label' => 'Sorties auxquelles je suis inscrit.e ',
            ])
            ->add('isNotInscrit', CheckboxType::class, [
                'label' => 'Sorties auxquelles je ne suis pas inscrit.e ',
            ])
            ->add('isPassee', CheckboxType::class, [
                'label' => 'Sorties passées ',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => FiltresSortiesFormModel::class,
        ]);
    }
}