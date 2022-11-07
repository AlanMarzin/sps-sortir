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
            $intervalleDebut = clone $sorties[$i]->getDateHeureDebut();
            $intervalleDebut = date_sub($intervalleDebut, new \DateInterval('P5D'));
            $sorties[$i]->setDateLimiteInscription($faker->dateTimeBetween($intervalleDebut, $sorties[$i]->getDateHeureDebut()));
            $sorties[$i]->setDuree($faker->numberBetween(10,200));
            $sorties[$i]->setInfosSortie($faker->paragraph);
            $sorties[$i]->setNbInscriptionsMax($faker->numberBetween(1,10));
            $sorties[$i]->setEtat($faker->randomElement($etats));
            $sorties[$i]->setOrganisateur($faker->randomElement($organisateur));
            $sorties[$i]->setLieu($faker->randomElement($lieu));
            $sorties[$i]->setCampus($faker->randomElement($campus));

            $manager->persist($sorties[$i]);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [EtatFixtures::class, UserFixtures::class, LieuFixtures::class];
    }

}
