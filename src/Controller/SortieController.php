<?php

namespace App\Controller;

use App\Repository\CampusRepository;
use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SortieController extends AbstractController
{
    #[Route('/', name: 'app_login')]
    public function list(AuthenticationUtils $authentificationUtils, SortieRepository $sortieRepository, CampusRepository $campusRepository): Response
    {
        //obtenir le login error s'il y en a un
        $error = $authentificationUtils->getLastAuthenticationError();

        //dernier username entré par l'utilisateur
        $lastUsername = $authentificationUtils->getLastUsername();

        // récupérer les sorties et campus en BDD
        $sorties = $sortieRepository->findAll();
        $campus = $campusRepository->findAll();

        return $this->render('sortie/listesorties.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error,
            'sorties' => $sorties,
            'campus' => $campus
        ]);
    }
}
