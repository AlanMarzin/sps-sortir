<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\FiltresSortiesType;
use App\Form\Model\FiltresSortiesFormModel;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use DateInterval;
use Doctrine\ORM\EntityManagerInterface;
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
            $sorties = $sortieRepository->findAllAffichables();
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

        $dateFin = clone $sortie->getDateHeureDebut();
        $dateFin->add(new DateInterval('PT' . $sortie->getDuree() . 'M'));
        return $this->render('sortie/detail.html.twig', [
            'sortie' => $sortie,
            'dateFin' => $dateFin
        ]);
    }

    #[Route('/inscription/{id}', name: 'sortie_inscription', requirements: ['id' => '\d+'])]
    public function inscription(EtatRepository $etatRepository, SortieRepository $sortieRepository, int $id, EntityManagerInterface $em): Response
    {
        // Récupérer la sortie en base de données
        $sortie = $sortieRepository->find($id);

        if ($sortie === null) {
            throw $this->createNotFoundException('Page not found');
        }

        // s'inscrire
        $sortie->addInscrit($this->getUser());
        // changer l'état de la sortie si le nombre max d'inscrits est atteint
        if ($sortie->getInscrits()->count() == $sortie->getNbInscriptionsMax()) {
            $sortie->setEtat($etatRepository->findOneBy(['libelle' => 'clôturée']));
        }
        $em->persist($sortie);
        $em->flush();

        return $this->redirectToRoute('sorties');
    }

    #[Route('/new', name: 'sortie_new')]
    public function newSortie(Request $request, EtatRepository $etatRepository, EntityManagerInterface $em): Response
    {
        // créer un objet sortie
        $sortie = new Sortie();
        $sortie->setEtat($etatRepository->findOneBy(['libelle' => 'en création']));
        $sortieForm = $this->createForm(SortieType::class, $sortie);

        // Récupération des données pour les insérer dans l'objet $serie
        $sortieForm->handleRequest($request);

        // Vérifier si l'utilisateur est en train d'envoyer le formulaire
        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {
            // Enregistrer la nouvelle sortie en BDD
            $sortie->setOrganisateur($this->getUser());
            $em->persist($sortie);
            $em->flush();

            $this->addFlash('success', 'La sortie a bien été créée !');

            // Rediriger l'internaute vers la liste des séries
            return $this->redirectToRoute('sorties');
        }

        return $this->render('sortie/new_sortie.html.twig', [
            'sortieForm' => $sortieForm->createView()
        ]);
    }

    #[Route('/desinscription/{id}', name: 'sortie_desinscription', requirements: ['id' => '\d+'])]
    public function desinscription(EtatRepository $etatRepository, SortieRepository $sortieRepository, int $id, EntityManagerInterface $em): Response
    {
        // Récupérer la sortie en base de données
        $sortie = $sortieRepository->find($id);
        $auj = date('d-m-Y');
        if ($sortie === null) {
            throw $this->createNotFoundException('Page not found');
        }

        // se désinscrire
        $sortie->removeInscrit($this->getUser());
        // changer l'état de la sortie si le nombre max d'inscrits n'est plus atteint et si la date limite n'est pas atteinte
        if (($sortie->getInscrits()->count() < $sortie->getNbInscriptionsMax()) and  !($sortie->getDateLimiteInscription() <  date('d-m-Y'))) {
            $sortie->setEtat($etatRepository->findOneBy(['libelle' => 'ouverte']));
        }
        $em->persist($sortie);
        $em->flush();

//        return $this->render('sortie/test.html.twig', [
//            'sortie' => $sortie,
//            'auj' => $auj
//        ]);

        // Rediriger l'internaute vers la liste des séries
        return $this->redirectToRoute('sorties');

    }

}
