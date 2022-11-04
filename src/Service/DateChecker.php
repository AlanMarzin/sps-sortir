<?php

namespace App\Service;

use App\Entity\Sortie;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use DateInterval;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class DateChecker
{

    public function checkDate(SortieRepository $sortieRepository, EtatRepository $etatRepository, EntityManagerInterface $em): array
    {
        // récupération des sorties et des états
        $sorties = $sortieRepository->findAll();
        $etats = $etatRepository->findAll();

        // récupération des index des états
        $indexCloturee = 0;
        $indexHistorisee = 0;
        $indexEnCours = 0;
        $indexTerminee = 0;
        for ($i=0; $i<count($etats); $i++) {
            if ($etats[$i]->getLibelle()=='clôturée') {
                $indexCloturee = $i;
            }
            if ($etats[$i]->getLibelle()=='historisée') {
                $indexHistorisee = $i;
            }
            if ($etats[$i]->getLibelle()=='en cours') {
                $indexEnCours = $i;
            }
            if ($etats[$i]->getLibelle()=='terminée') {
                $indexTerminee = $i;
            }
        }

        $now = new DateTime("now");
        $monthAgo = clone $now;
        $monthAgo->modify('-1 month');

        // changement d'état des sorties si nécessaire
        foreach ($sorties as $sortie) {
            // si la date limite d'inscription est passée, changer l'état pour "clôturée"
            if ($sortie->getDateLimiteInscription() < $now) {
//                $sortie->setEtat($etatRepository->findOneBy(['libelle' => 'clôturée']));
                $sortie->setEtat($etats[$indexCloturee]);
            }

            // si la sortie date d'il y a un mois ou plus, changer l'état pour "historisée"
            if ($sortie->getDateHeureDebut() < $monthAgo) {
//                $sortie->setEtat($etatRepository->findOneBy(['libelle' => 'historisée']));
                $sortie->setEtat($etats[$indexHistorisee]);
            }

            // si la sortie a commencé, changer l'état pour "en cours"
            if ($sortie->getDateHeureDebut() <= $now) {
//                $sortie->setEtat($etatRepository->findOneBy(['libelle' => 'en cours']));
                $sortie->setEtat($etats[$indexEnCours]);
            }

            // si la sortie est terminée, changer l'état pour "terminée"
            $heureFinSortie = date_add($sortie->getDateHeureDebut(), new DateInterval('PT'.$sortie->getDuree().'M'));
            if ($heureFinSortie <= $now) {
//                $sortie->setEtat($etatRepository->findOneBy(['libelle' => 'terminée']));
                $sortie->setEtat($etats[$indexTerminee]);
            }

        }

        $em->flush();

        // retourner les sorties
        return $sorties;

    }

}