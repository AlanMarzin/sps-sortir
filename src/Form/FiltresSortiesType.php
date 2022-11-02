<?php

namespace App\Form;

use App\Entity\Campus;
use App\Form\Model\FiltresSortiesFormModel;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FiltresSortiesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('campus', EntityType::class, [
                'class' => Campus::class,
                'choice_label' => 'nom',
                'label' => 'Campus ',
            ])
            ->add('nomRecherche', SearchType::class, [
                'label' => 'Le nom de la sortie contient : ',
                'required' => false
            ])
            ->add('dateDebut', DateType::class, [
                'label' => 'Entre ',
                'widget' => 'single_text',
                'required' => false
            ])
            ->add('dateFin', DateType::class, [
                'label' => 'et ',
                'widget' => 'single_text',
                'required' => false
            ])
            ->add('isOrganisateur', CheckboxType::class, [
                'label' => 'Sorties dont je suis l\'organisateur.trice ',
                'required' => false
            ])
            ->add('isInscrit', CheckboxType::class, [
                'label' => 'Sorties auxquelles je suis inscrit.e ',
                'required' => false
            ])
            ->add('isNotInscrit', CheckboxType::class, [
                'label' => 'Sorties auxquelles je ne suis pas inscrit.e ',
                'required' => false
            ])
            ->add('isPassee', CheckboxType::class, [
                'label' => 'Sorties passées ',
                'required' => false
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
