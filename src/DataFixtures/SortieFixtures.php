<?php

namespace App\DataFixtures;

use App\Entity\Sortie;
use App\Entity\Etat;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class SortieFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $escalade = new Sortie();
        $escalade->setNom('Grimpette au Roof');
        $escalade->setDateHeureDebut(\DateTime::createFromFormat('Y-m-d H:i:s', '2022-11-02 17:30:00'));
        $escalade->setDuree(120);
        $escalade->setDateLimiteInscription(\DateTime::createFromFormat('Y-m-d', '2022-10-31'));
        $escalade->setNbInscriptionsMax(10);
        $escalade->setInfosSortie('Sortie au Roof (Hôtel Dieu), grimpe, bière.');
        $escalade->setEtat($this->getReference('etat-cloturee'));
        $manager->persist($escalade);

        $muscu = new Sortie();
        $muscu->setNom('Muscle Up à Fitness Park');
        $muscu->setDateHeureDebut(\DateTime::createFromFormat('Y-m-d H:i:s', '2022-11-15 18:30:00'));
        $muscu->setDuree(60);
        $muscu->setDateLimiteInscription(\DateTime::createFromFormat('Y-m-d', '2022-11-13'));
        $muscu->setNbInscriptionsMax(3);
        $muscu->setInfosSortie('GymBros Night à Fitness Park');
        $muscu->setEtat($this->getReference('etat-ouverte'));
        $manager->persist($muscu);

        $moto = new Sortie();
        $moto->setNom('Sortie Moto');
        $moto->setDateHeureDebut(\DateTime::createFromFormat('Y-m-d H:i:s', '2022-11-08 12:30:00'));
        $moto->setDuree(30);
        $moto->setDateLimiteInscription(\DateTime::createFromFormat('Y-m-d', '2022-11-07'));
        $moto->setNbInscriptionsMax(4);
        $moto->setInfosSortie('Sortie moto à l\'heure du déj');
        $moto->setEtat($this->getReference('etat-creation'));
        $manager->persist($moto);

        $manager->flush();
    }

    public function getDependencies()
    {
        return [EtatFixtures::class];
    }

}
