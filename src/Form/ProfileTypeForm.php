<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ProfileTypeForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            //Pour la gestion des Photos de profil
//            ->add('photoProfil', FileType::class, [
//                'mapped' => true,
//                'label' => 'Modifier votre Photo : '
//            ])
            ->add('email')
            ->add('password', PasswordType::class, [
                'label'=> 'Mot de Passe : ',
                'required'=>false,
                'mapped' => false,
                'attr' =>['autocomplete'=>'nouveau mot de passe'],
                'constraints'=>[
                    new Length([
                        'min' => 3,
                        'minMessage' => 'Votre mot de passe doit être de 3 caractères minimun',
                        'max' => 4096,
                    ]),
                ]
            ])
            ->add('nom', TextType::class, [
                'label'=>'Nom : '
            ])
            ->add('prenom', TextType::class, [
                'label'=>'Prénom : '
            ])
            ->add('pseudo', TextType::class, [
                'label'=>'Pseudo : '
            ])
            ->add('telephone', TextType::class, [
                'label'=>'Numéro de téléphone : '
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}