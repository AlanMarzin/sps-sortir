<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Sortie;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
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
                'label'=>'Titre : '
            ])
            ->add('dateHeureDebut', DateTimeType::class, [
                'label'=>'Date de début : ',
                'widget'=>'single_text'
            ])
            ->add('duree', IntegerType::class, [
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

            ->add('lieu', EntityType::class, [
                'class'=>Lieu::class,
                'choice_label'=>'nom',
                'label'=>'Lieu : ',
            ])

//             ->add('campus', EntityType::class, [
//                 'class'=>Campus::class,
//                 'choice_label'=>'nom',
//                 'label'=>'Campus : '
//             ])

            ->add('campus', EntityType::class, [
                'class'=>Campus::class,
//                'query_builder'=> function (EntityRepository $er){
//                return $er->createQueryBuilder('c')->orderBy('c.nom', 'ASC');
//                },
                'choice_label'=>'nom',
                'label'=>'Campus : ',
                'data'=> $this->security->getUser()->getCampus()

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
