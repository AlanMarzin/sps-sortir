<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Ville;
use phpDocumentor\Reflection\Types\Collection;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LieuType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label'=>'Nom du lieu : '
            ])
            ->add('rue', TextType::class, [
                'label'=>'Nom de la rue : '
            ])
            ->add('latitude', NumberType::class, [
                'label'=>'Latitude : ',
                'required'=>false
            ])
            ->add('longitude', NumberType::class, [
                'label'=>'Longitude : ',
                'required'=>false

            ])
            ->add('ville', EntityType::class, [
                'class'=>Ville::class,
                'choice_label'=>'nom',
                'label'=>'Ville : '
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Lieu::class,
        ]);
    }
}
