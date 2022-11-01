<?php

namespace App\Controller;

use App\Repository\CampusRepository;
use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/sortie')]
class SortieController extends AbstractController
{
    #[Route('/', name: 'sortie_list')]
    public function list(SortieRepository $sortieRepository, CampusRepository $campusRepository): Response
    {
        // récupérer les sorties et campus en BDD
        $sorties = $sortieRepository->findAll();
        $campus = $campusRepository->findAll();

        return $this->render('sortie/listesorties.html.twig', [
            'sorties' => $sorties,
            'campus' => $campus
        ]);
    }
}
