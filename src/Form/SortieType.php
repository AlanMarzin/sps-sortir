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
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
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
        /** @var Sortie|null $sortie */
        $sortie = $options['data'] ?? null;

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
            ->add('enregistrer', SubmitType::class, [
                'label' => 'Enregistrer'
            ])
            ->add('publier', SubmitType::class, [
                'label' => 'Publier la sortie'
            ]);

//        $builder->addEventListener(
//            FormEvents::PRE_SET_DATA,
//            function (FormEvent $event) {
//                /** @var Sortie|null $data */
//                $data = $event->getData();
//                if (!$data) {
//                    return;
//                }
//                $this->setupLieuNameField(
//                    $event->getForm(),
//                    $data->getLieu()->getVille()
//                );
//            }
//        );
//
//        $builder->get('ville')->addEventListener(
//            FormEvents::POST_SUBMIT,
//            function(FormEvent $event) {
//                $form = $event->getForm();
//                $this->setupLieuNameField(
//                    $form->getParent(),
//                    $form->getData()
//                );
//            }
//        );

//        $builder->addEventListener(FormEvents::PRE_SET_DATA, array($this, 'onPreSetData'));
//        $builder->addEventListener(FormEvents::PRE_SUBMIT, array($this, 'onPreSubmit'));

//        $builder->add('lieu', EntityType::class, [
//                'placeholder' => 'Lieux ici',
//                'class'=>Lieu::class,
//                'choice_label'=>'nom',
//                'label'=>'Lieu : ',
//                'required' => false
//        ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }

//    protected function addElements(FormInterface $form, Ville $ville = null) {
//
//        // ajouter le select Ville
//        $form->add('ville', EntityType::class, [
//            'class'=>Ville::class,
//            'mapped' => false,
//            'choice_label'=>'nom',
//            'label'=>'Ville : ',
//            'required' => true,
//            'data' => $ville,
//            'placeholder' => 'Sélectionnez une ville...',
//        ]);
//
//        // Lieux vide, sauf si une ville est sélectionnée
//        $lieux = [];

        // If there is a ville stored in the Sortie entity, load the lieux of it
//        if ($ville) {
//            // Fetch Neighborhoods of the City if there's a selected city
//            $lieuxRepo = $this->em->getRepository(Lieu::class);
//
//            $lieux = $lieuxRepo->createQueryBuilder('l')
//                ->where("l.ville = :villeid")
//                ->setParameter("villeid", $ville->getId())
//                ->getQuery()
//                ->getResult();
//        }

//        // Add the Neighborhoods field with the proper data
//        $form->add('lieu', EntityType::class, [
//            'placeholder' => 'Choisissez d`\'abord une ville...',
//            'class'=>Lieu::class,
//            'choice_label'=>'nom',
//            'label'=>'Lieu : ',
//            'required' => true,
//            'choices' => $lieux
//        ]);
//    }
//
//    function onPreSubmit(FormEvent $event) {
//        $form = $event->getForm();
//        $data = $event->getData();
//
//        // Search for selected City and convert it into an Entity
//        $ville = $this->em->getRepository(Ville::class)->find($data['ville']);
//
//        $this->addElements($form, $ville);
//    }
//
//    function onPreSetData(FormEvent $event) {
//        $sortie = $event->getData();
//        $form = $event->getForm();

        // When you create a new sortie, the Ville is always empty
//        $ville = $sortie->getVille() ? $sortie->getLieu() : null;
//        $ville = null;
//
//        $this->addElements($form, $ville);
//    }

//    private function getLieuxChoices(string $ville)
//    {
//        $ville1 = [
//            'lieu1',
//            'lieu2',
//            'lieu3'
//        ];
//
//        $lieuxChoices = [
//            'ville1' => array_combine($ville1, $ville1),
//        ];
//        return $lieuxChoices[$ville];
//    }
//
//    private function setupLieuNameField(FormInterface $form, ?string $ville)
//    {
//        if (null === $ville) {
//            $form->remove('lieu');
//            return;
//        }
//
//        $choices = $this->getLieuxChoices($ville);
//        if (null === $choices) {
//            $form->remove('lieu');
//            return;
//        }
//
//        $form->add('lieu', EntityType::class, [
//                'placeholder' => 'Lieux ici',
//                'class'=>Lieu::class,
//                'choice_label'=>'nom',
//                'label'=>'Lieu : ',
//                'required' => false
//        ]);
//
//    }
}
