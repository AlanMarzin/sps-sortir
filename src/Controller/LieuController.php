<?php

namespace App\Controller;


use App\Entity\Lieu;
use App\Form\LieuType;
use App\Form\SortieType;
use Container7hDeg5A\getSortieTypeService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LieuController extends AbstractController
{
    #[Route('/lieu', name: 'new_lieu', requirements: ['id' => '\d+'])]
    public function newLieu( Request $request, EntityManagerInterface $em, ): Response
    {
        //Créer un objet lieu
        $lieu = new Lieu();
        $lieuForm = $this->createForm(LieuType::class, $lieu);
        $sortieForm = $this->createForm(SortieType::class);

        //Recuperation des données pour les insérer dans l'objet $lieu
        $lieuForm->handleRequest($request);

        // Vérifier si l'utilisateur est en train d'envoyer le formulaire
        if ($lieuForm->isSubmitted() && $lieuForm->isValid()) {
            // Enregistrer la nouvelle sortie en BDD
            $em->persist($lieu);
            $em->flush();
        }
        return $this->render('sortie/new_lieu.html.twig', [
            //'controller_name' => 'LieuController',
            'lieuForm' => $lieuForm->createView(),
            'sortieForm' => $sortieForm->createview(),
        ]);
    }
}
