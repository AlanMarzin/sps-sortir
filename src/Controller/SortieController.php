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
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SortieController extends AbstractController
{
    #[Route('/', name: 'sorties')]
    public function list(Request $request, AuthenticationUtils $authentificationUtils, SortieRepository $sortieRepository, CampusRepository $campusRepository): Response
    {

//        /*
//        Login
//        */
//        //obtenir le login error s'il y en a un
//        $error = $authentificationUtils->getLastAuthenticationError();
//
//        //dernier username entré par l'utilisateur
//        $lastUsername = $authentificationUtils->getLastUsername();

        /*
        Affichage des sorties
        */
        // créer le formulaire de filtres
        $filtresSorties = new FiltresSortiesFormModel();
        $filtresSortiesForm = $this->createForm(FiltresSortiesType::class, $filtresSorties);
        $filtresSortiesForm->handleRequest($request);

        // traitement du formulaire de filtres
        if ($filtresSortiesForm->isSubmitted() && $filtresSortiesForm->isValid()) {
            var_dump($filtresSorties);
        }

        // récupérer les sorties et campus en BDD
        $sorties = $sortieRepository->findAll();

        return $this->render('sortie/listesorties.html.twig', [
//            'last_username' => $lastUsername,
//            'error'         => $error,
            'filtresSortiesForm' => $filtresSortiesForm->createView(),
            'sorties' => $sorties,
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
