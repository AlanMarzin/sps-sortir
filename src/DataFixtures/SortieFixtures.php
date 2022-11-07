<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\User;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker;

class SortieFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {

        $etats = $manager->getRepository(Etat::class)->findAll();
        $organisateur = $manager->getRepository(User::class)->findAll();
        $lieu = $manager->getRepository(Lieu::class)->findAll();
        $campus = $manager->getRepository(Campus::class)->findAll();

        $faker = Faker\Factory::create('fr_FR');

        $sorties = Array();
        for ($i = 0; $i < 50; $i++) {
            $sorties[$i] = new Sortie();
            $sorties[$i]->setNom($faker->sentence(3));
            $sorties[$i]->setDateHeureDebut($faker->dateTimeBetween('-3 months', '+3 months'));
            $sorties[$i]->setDateLimiteInscription($faker->dateTimeBetween('-3 months', $sorties[$i]->getDateHeureDebut()));
            $sorties[$i]->setDuree($faker->numberBetween(10,200));
            $sorties[$i]->setInfosSortie($faker->paragraph);
            $sorties[$i]->setNbInscriptionsMax($faker->numberBetween(1,10));
            $sorties[$i]->setEtat($faker->randomElement($etats));
            $sorties[$i]->setOrganisateur($faker->randomElement($organisateur));
            $sorties[$i]->setLieu($faker->randomElement($lieu));
            $sorties[$i]->setCampus($faker->randomElement($campus));

            $manager->persist($sorties[$i]);
        }

        $escalade = new Sortie();
        $escalade->setNom('Grimpette au Roof');
        $escalade->setDateHeureDebut(DateTime::createFromFormat('d-m-y H:i:s', '01-10-22 17:30:00'));
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
        $moto->setNbInscriptionsMax(3);
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
