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
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SortieController extends AbstractController
{

    #[Route('/', name: 'sorties')]
    public function index(Request $request, SortieRepository $sortieRepository, EtatRepository $etatRepository, EntityManagerInterface $em, DateChecker $dateChecker): Response
    {

        if ($this->getUser() == null) {
            return $this->redirectToRoute('app_plzlogin');

        } else {
            // màj l'état des sorties en BDD
            $dateChecker->checkDate($sortieRepository, $etatRepository, $em);

            $currentUser = $this->getUser();

            // créer le formulaire de filtres
            $filtresSorties = new FiltresSortiesFormModel();
            $filtresSortiesForm = $this->createForm(FiltresSortiesType::class, $filtresSorties);
            $filtresSortiesForm->handleRequest($request);

            // traiter du formulaire de filtres ou faire une recherche sur le campus de l'utilisateur uniquement
            if ($filtresSortiesForm->isSubmitted() && $filtresSortiesForm->isValid()) {
                $sorties = $sortieRepository->findByFiltresSorties($filtresSorties, $currentUser);
            } else {
                $sorties = $sortieRepository->findAllAffichables($currentUser);
            }

            return $this->render('sortie/listesorties.html.twig', [
                'filtresSortiesForm' => $filtresSortiesForm->createView(),
                'sorties' => $sorties,
                'filtresSorties' => $filtresSorties
            ]);
        }

    }

    //permet d'accéder à la page de détail pour annuler une sortie
    #[Route('/sortie/recapAnnuler/{id}', name: 'annuler_detail', requirements: ['id' => '\d+'])]
    public function recapAnnulSortie(SortieRepository $sortieRepository, int $id): Response
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
        if ($sortie->getDateLimiteInscription() <= new DateTime("now")) {
            $this->addFlash('error', 'Vous ne pouvez pas vous inscrire à cette sortie, la date limite est dépassée !');
        } else if ($sortie->getNbInscriptionsMax()===count($sortie->getInscrits())) {
            $this->addFlash('error', 'Vous ne pouvez pas vous inscrire à cette sortie, le nombre maximal de participants est atteint !');
        } else {
            $sortie->addInscrit($this->getUser());
            // changer l'état de la sortie si le nombre max d'inscrits est atteint
            if ($sortie->getInscrits()->count() == $sortie->getNbInscriptionsMax()) {
                $sortie->setEtat($etatRepository->findOneBy(['libelle' => 'clôturée']));
            }
            $em->flush();
        }

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
        if ($sortie->getDateLimiteInscription() <= new DateTime("now")) {
            $this->addFlash('error', 'Vous ne pouvez pas vous désinscrire de cette sortie, la date limite est dépassée !');
        } else if (!in_array($sortie, $this->getUser()->getSortiesInscrit()->toArray())) {
            $this->addFlash('error', 'Vous ne pouvez pas vous désinscrire de cette sortie, vous n`\'êtes pas inscrit !');
        } else {
            $sortie->removeInscrit($this->getUser());
            // changer l'état de la sortie si le nombre max d'inscrits n'est plus atteint et si la date limite n'est pas atteinte
            if (($sortie->getInscrits()->count() < $sortie->getNbInscriptionsMax()) and  !($sortie->getDateLimiteInscription() <  date('d-m-Y'))) {
                $sortie->setEtat($etatRepository->findOneBy(['libelle' => 'ouverte']));
            }
            $em->flush();
        }

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
        $sortie->setInfosSortie($sortie->getInfosSortie()."\n\nMalheureusement cette sortie a dû être annulée pour le motif suivant :\n\n".$motif);
        $em->flush();
        $this->addFlash('success', 'Votre sortie a bien été annulée !');

        // Rediriger l'internaute vers la liste des sorties
        return $this->redirectToRoute('sorties');

    }

    #[Route('/sortie/modification/{id}', name: 'sortie_modifier', requirements: ['id' => '\d+'])]
    public function modifier(Request $request, SortieRepository $sortieRepository, EtatRepository $etatRepository, int $id, EntityManagerInterface $em): Response
    {

        // Récupérer la sortie à afficher en base de données
        $sortie = $sortieRepository->find($id);
        if ($sortie === null) {
            throw $this->createNotFoundException('Page not found');
        }

        if ($sortie->getOrganisateur() != $this->getUser()) {
            $this->addFlash('error', 'Vous ne pouvez pas modifier cette sortie !');
            return $this->redirectToRoute('sorties');
        } else {
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

    }

    #[Route('/sortie/publier/{id}', name: 'sortie_publier', requirements: ['id' => '\d+'])]
    public function publier(Request $request, SortieRepository $sortieRepository, EtatRepository $etatRepository, int $id, EntityManagerInterface $em): Response
    {

        // Récupérer la sortie à afficher en base de données
        $sortie = $sortieRepository->find($id);
        if ($sortie === null) {
            throw $this->createNotFoundException('Page not found');
        }

        if ($sortie->getOrganisateur() != $this->getUser()) {
            $this->addFlash('error', 'Vous ne pouvez pas publier cette sortie !');
            return $this->redirectToRoute('sorties');
        } else {
            $sortie->setEtat($etatRepository->findOneBy(['libelle' => 'ouverte']));
                $em->flush();
                $this->addFlash('success', 'Votre sortie a bien été publiée !');
        }
        return $this->redirectToRoute('sorties');

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
