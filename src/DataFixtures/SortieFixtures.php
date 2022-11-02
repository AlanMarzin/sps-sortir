<?php

namespace App\DataFixtures;

use App\Entity\Sortie;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class SortieFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $escalade = new Sortie();
        $escalade->setNom('Grimpette au Roof');
        $escalade->setDateHeureDebut(DateTime::createFromFormat('d-m-y H:i:s', '02-11-22 17:30:00'));
        $escalade->setDateLimiteInscription(DateTime::createFromFormat('d-m-y', '31-10-22'));
        $escalade->setDuree(180);
        $escalade->setInfosSortie('Session grimpe puis session bière.');
        $escalade->setNbInscriptionsMax(10);
        $escalade->setEtat($this->getReference('etat-terminee'));
        $escalade->setOrganisateur($this->getReference('user-axelle'));
        $escalade->setLieu($this->getReference('lieu-roof'));
        $escalade->setCampus($this->getReference('campus-rennes'));
        $manager->persist($escalade);

        $muscu = new Sortie();
        $muscu->setNom('Musculation');
        $muscu->setDateHeureDebut(DateTime::createFromFormat('d-m-y H:i:s', '22-11-22 18:00:00'));
        $muscu->setDateLimiteInscription(DateTime::createFromFormat('d-m-y', '20-11-22'));
        $muscu->setDuree(90);
        $muscu->setInfosSortie('Concours de muscle up chez Fitness Land');
        $muscu->setNbInscriptionsMax(3);
        $muscu->setEtat($this->getReference('etat-creation'));
        $muscu->setOrganisateur($this->getReference('user-alan'));
        $muscu->setLieu($this->getReference('lieu-fitnessLand'));
        $muscu->setCampus($this->getReference('campus-rennes'));
        $manager->persist($muscu);

        $moto = new Sortie();
        $moto->setNom('Balade en moto');
        $moto->setDateHeureDebut(DateTime::createFromFormat('d-m-y H:i:s', '10-11-22 12:30:00'));
        $moto->setDateLimiteInscription(DateTime::createFromFormat('d-m-y', '08-11-22'));
        $moto->setDuree(60);
        $moto->setInfosSortie('Balade en moto à l\'heure du déjeuner.');
        $moto->setNbInscriptionsMax(6);
        $moto->setEtat($this->getReference('etat-ouverte'));
        $moto->setOrganisateur($this->getReference('user-fred'));
        $moto->setLieu($this->getReference('lieu-rocade'));
        $moto->setCampus($this->getReference('campus-rennes'));
        $manager->persist($moto);

        $manager->flush();
    }

    public function getDependencies()
    {
        return [EtatFixtures::class, UserFixtures::class, LieuFixtures::class];
    }

}
