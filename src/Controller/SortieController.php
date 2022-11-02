<?php

namespace App\Controller;

use App\Form\FiltresSortiesType;
use App\Form\Model\FiltresSortiesFormModel;
use App\Repository\CampusRepository;
use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class SortieController extends AbstractController
{
    #[Route('/', name: 'sorties')]
    public function list(Request $request, SortieRepository $sortieRepository): Response
    {

        $currentUser = $this->getUser();

        // créer le formulaire de filtres
        $filtresSorties = new FiltresSortiesFormModel();
        $filtresSortiesForm = $this->createForm(FiltresSortiesType::class, $filtresSorties);
        $filtresSortiesForm->handleRequest($request);

        // traitement du formulaire de filtres
        if ($filtresSortiesForm->isSubmitted() && $filtresSortiesForm->isValid()) {
            $sorties = $sortieRepository->findByFiltresSorties($filtresSorties, $currentUser);
        } else {
            // récupérer toutes les sorties en BDD
            $sorties = $sortieRepository->findAll();
        }

        return $this->render('sortie/listesorties.html.twig', [
            'filtresSortiesForm' => $filtresSortiesForm->createView(),
            'sorties' => $sorties,
            'filtresSorties' => $filtresSorties
        ]);

    }

    #[Route('/{id}', name: 'sortie_detail', requirements: ['id' => '\d+'])]
    public function detail(SortieRepository $sortieRepository, int $id): Response
    {
        // Récupérer la sortie à afficher en base de données
        $sortie = $sortieRepository->find($id);

        if ($sortie === null) {
            throw $this->createNotFoundException('Page not found');
        }

        return $this->render('sortie/detail.html.twig', [
            'sortie' => $sortie
        ]);
    }

}
