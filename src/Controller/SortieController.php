<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Sortie;
use App\Form\FiltresSortiesType;
use App\Form\Model\FiltresSortiesFormModel;
use App\Form\UpdateSortieType;
use App\Repository\EtatRepository;
use App\Form\SortieType;
use App\Repository\SortieRepository;
use App\Service\DateChecker;
use DateInterval;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SortieController extends AbstractController
{

    #[Route('/', name: 'sorties')]
    public function list(Request $request, SortieRepository $sortieRepository, EtatRepository $etatRepository, EntityManagerInterface $em, DateChecker $dateChecker): Response
    {

//        $sorties = $dateChecker->checkDate($sortieRepository, $etatRepository, $em);
        $dateChecker->checkDate($sortieRepository, $etatRepository, $em);

        $currentUser = $this->getUser();

        // créer le formulaire de filtres
        $filtresSorties = new FiltresSortiesFormModel();
        $filtresSortiesForm = $this->createForm(FiltresSortiesType::class, $filtresSorties);
        $filtresSortiesForm->handleRequest($request);

        // traitement du formulaire de filtres
        if ($filtresSortiesForm->isSubmitted() && $filtresSortiesForm->isValid()) {
            $sorties = $sortieRepository->findByFiltresSorties($filtresSorties, $currentUser);
        } else {
            $sorties = $sortieRepository->findAllAffichables();
        }

        // récupérer toutes les sorties affichables en retirant les historisées et en création par d'autres gens
        for ($i=0; $i<count($sorties); $i++) {
            if (($sorties[$i]->getEtat()->getLibelle() === 'en création' and $sorties[$i]->getOrganisateur() != $currentUser) or $sorties[$i]->getEtat()->getLibelle() === 'historisée') {
                unset($sorties[$i]);
            }
        }

        return $this->render('sortie/listesorties.html.twig', [
            'filtresSortiesForm' => $filtresSortiesForm->createView(),
            'sorties' => $sorties,
            'filtresSorties' => $filtresSorties
        ]);

    }

    //permet d'accéder à la page de détail pour annuler une sortie
    #[Route('/sortie/recapAnnuler/{id}', name: 'annuler_detail', requirements: ['id' => '\d+'])]
    public function recapAnnulSorti(SortieRepository $sortieRepository, int $id): Response
    {
        // Récupérer la sortie à afficher en base de données
        $sortie = $sortieRepository->find($id);

        if ($sortie === null) {
            throw $this->createNotFoundException('Page not found');
        }

        return $this->render('sortie/annulersortie.html.twig', [
            'sortie' => $sortie,
            'id' => $id
        ]);
    }


    #[Route('/sortie/inscription/{id}', name: 'sortie_inscription', requirements: ['id' => '\d+'])]
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
        $em->flush();

        return $this->redirectToRoute('sorties');
    }

    #[Route('/sortie/new', name: 'sortie_new')]
    public function newSortie(Request $request, EtatRepository $etatRepository, EntityManagerInterface $em): Response
    {
        // créer un objet sortie
        $sortie = new Sortie();
        $sortieForm = $this->createForm(SortieType::class, $sortie);

        // Récupération des données pour les insérer dans l'objet $sortie
        $sortieForm->handleRequest($request);

        // Vérifier si l'utilisateur est en train d'envoyer le formulaire
        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {

            //Récupère le nom de l'organisateur
            $sortie->setOrganisateur($this->getUser());

            // si j'ai cliqué sur le bouton publier, passer l'état en 'ouverte'
            if ($sortieForm->get('publier')->isClicked()) {
                $sortie->setEtat($etatRepository->findOneBy(['libelle' => 'ouverte']));
                // si j'ai cliqué sur le bouton enregistrer, passer l'état en 'en création'
            } else if ($sortieForm->get('enregistrer')->isClicked()) {
                $sortie->setEtat($etatRepository->findOneBy(['libelle' => 'en création']));
            }

            // inscrire l'orga à la sortie
            $sortie->addInscrit($this->getUser());

            // Enregistrer la nouvelle sortie en BDD
            $em->persist($sortie);
            $em->flush();

            $this->addFlash('success', 'Votre sortie a bien été créée !');

            // Rediriger l'internaute vers la liste des sorties
            return $this->redirectToRoute('sorties');
        }

        return $this->render('sortie/new_sortie.html.twig', [
            'sortieForm' => $sortieForm->createView()
        ]);
    }

    #[Route('/sortie/desinscription/{id}', name: 'sortie_desinscription', requirements: ['id' => '\d+'])]
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
        $em->flush();

        // Rediriger l'internaute vers la liste des séries
        return $this->redirectToRoute('sorties');

    }

    //permet d'annuler une sortie
    #[Route('/sortie/annulation/{id}', name: 'sortie_annuler', requirements: ['id' => '\d+'])]
    public function annuler(EtatRepository $etatRepository, SortieRepository $sortieRepository, int $id, EntityManagerInterface $em): Response
    {
        // Récupérer la sortie en base de données
        $sortie = $sortieRepository->find($id);
        $motif = $_POST['motif'];

        if ($sortie === null) {
            throw $this->createNotFoundException('Page not found');
        }

        // changer l'état de la sortie si l'utilisateur clique
        $sortie->setEtat($etatRepository->findOneBy(['libelle' => 'annulée']));
        $sortie->setInfosSortie($sortie->getInfosSortie()."ANNULÉE".$motif);
        $em->flush();

        // Rediriger l'internaute vers la liste des sorties
        return $this->redirectToRoute('sorties');

    }

    #[Route('/sortie/modification/{id}', name: 'sortie_modifier', requirements: ['id' => '\d+'])]
    public function modifier(Request $request, SortieRepository $sortieRepository, EtatRepository $etatRepository, int $id, EntityManagerInterface $em): Response
    {
        // Récupérer la sortie à afficher en base de données
        $sortie = $sortieRepository->findOneBy(['id' => $id]);
        if ($sortie === null) {
            throw $this->createNotFoundException('Page not found');
        }

        $sortieForm = $this->createForm(UpdateSortieType::class, $sortie);
        $sortieForm->handleRequest($request);

        // Vérifier si l'utilisateur est en train d'envoyer le formulaire
        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {

            // si j'ai cliqué sur le bouton enregistrer, màj la sortie
            if ($sortieForm->get('enregistrer')->isClicked()) {
                $em->flush();
                $this->addFlash('success', 'Votre sortie a bien été mise à jour !');
            // si j'ai cliqué sur publier, passer l'état de la sortie à ouverte
            } else if ($sortieForm->get('publier')->isClicked()) {
                $sortie->setEtat($etatRepository->findOneBy(['libelle' => 'ouverte']));
                $em->flush();
                $this->addFlash('success', 'Votre sortie a bien été mise à jour !');
            // si j'ai cliqué sur supprimer, supprimer la sortie et revenir à la liste des sorties
            } else if ($sortieForm->get('supprimer')->isClicked()) {
                if ($this->isCsrfTokenValid('delete' . $sortie->getId(), $request->request->get('_token'))) {
                    $em->remove($sortie);
                    $em->flush();
                    $this->addFlash('success', 'Votre sortie a bien été supprimée !');
                } else {
                    $this->addFlash('error', 'Le token CSRF est invalide !');
                }
            // si j'ai cliqué sur annuler, revenir à la liste des sorties
            }
                return $this->redirectToRoute('sorties');
        }

            return $this->render('sortie/modifier_sortie.html.twig', [
                'sortieForm' => $sortieForm->createView()
            ]);
    }


    #[Route('/sortie/{slug}', name: 'sortie_detail')]
    public function detail(SortieRepository $sortieRepository, string $slug): Response
    {
        // Récupérer la sortie à afficher en base de données
        $sortie = $sortieRepository->findOneBy(['slug' => $slug]);

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

}
