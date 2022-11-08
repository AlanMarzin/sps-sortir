<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            //Pour la gestion des Photos de profil
//            ->add('photoProfil', FileType::class, [
//                'mapped' => true,
//                'label' => 'Modifier votre Photo : '
//            ])
            ->add('email',EmailType::class, [
                'label'=> 'Email : '
            ])

            ->add('password', PasswordType::class, [
                'label'=> 'Mot de Passe : ',
                'required'=>false,
                'mapped' =>false,
                'constraints'=>[
                    new Length([
                        'min' => 3,
                        'minMessage' => 'Votre mot de passe doit être de 3 caractères minimun',
                        'max' => 4096,
                    ]),
                ]
            ])
            ->add('passwordConfirmation', PasswordType::class, [
                'label'=> 'Confirmation : ',
                'required'=>false,
                'mapped'=>false,
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
            ->add('photo', FileType::class, [
                'label'=>'Photo : ',
                'mapped'=> false, //permet de ne pas rendre obligatoire l'image
                'required'=> false,
            ])
            ->add('telephone', TextType::class, [
                'label'=>'Téléphone : '
            ])
            ->add('campus' ,EntityType::class, [
                'class' => Campus::class,
                'choice_label' => 'nom',
                'label' => 'Campus ',
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
