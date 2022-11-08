<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Ville;
use App\Entity\Lieu;
use App\Entity\Sortie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class SortieType extends AbstractType
{
    private $security;
    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label'=>'Nom de la sortie : '
            ])
            ->add('dateHeureDebut', DateTimeType::class, [
                'label'=>'Date et heure de la sortie : ',
                'widget'=>'single_text'
            ])
            ->add('dateLimiteInscription', DateType::class, [
                'label'=>'Date limite d\'inscription : ',
                'widget'=>'single_text'
            ])
            ->add('nbInscriptionsMax', IntegerType::class, [
                'label'=>'Nombre de places : '
            ])
            ->add('duree', IntegerType::class, [
                'label'=>'Durée de l\'activité : '
            ])
            ->add('infosSortie', TextareaType::class, [
                'label'=>'Description et infos : '
            ])
            ->add('campus', EntityType::class, [
                'class'=>Campus::class,
                'choice_label'=>'nom',
                'label'=>'Campus : ',
                'data'=> $this->security->getUser()->getCampus()
            ])
            ->add('ville', EntityType::class, [
                'class'=>Ville::class,
                'mapped' => false,
                'choice_label'=>'nom',
                'label'=>'Ville : ',
                'required' => false
            ])
            ->add('lieu', EntityType::class, [
                'placeholder' => 'Lieux ici',
                'class'=>Lieu::class,
                'choice_label'=>'nom',
                'label'=>'Lieu : ',
                'required' => false
            ])
            ->add('enregistrer', SubmitType::class, [
                'label' => 'Enregistrer'
            ])
            ->add('publier', SubmitType::class, [
                'label' => 'Publier la sortie'
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
