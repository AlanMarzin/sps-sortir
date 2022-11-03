<?php

namespace App\Service;

use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class DateChecker
{

    public function checkDate(SortieRepository $sortieRepository, EtatRepository $etatRepository, EntityManagerInterface $em): void
    {
        $sorties = $sortieRepository->findAll();
        $now = new DateTime("now");
        $monthAgo = clone $now;
        $monthAgo->modify('-1 month');

        foreach ($sorties as $sortie) {
            // si la date limite d'inscription est passée, changer l'état pour "clôturée"
            if ($sortie->getDateLimiteInscription() < $now) {
                $sortie->setEtat($etatRepository->findOneBy(['libelle' => 'clôturée']));
            }

            // si la sortie date d'il y a un mois ou plus, changer l'état pour "historisée"
            if ($sortie->getDateHeureDebut() < $monthAgo) {
                $sortie->setEtat($etatRepository->findOneBy(['libelle' => 'historisée']));
            }
            dump($sortie->getDateHeureDebut());
            dump($sortie->getDateHeureDebut() < $monthAgo);
        }

        $em->flush();

    }

}