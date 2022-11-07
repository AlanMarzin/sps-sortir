<?php

namespace App\DataFixtures;

use App\Entity\Ville;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;

class VilleFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        $faker = Faker\Factory::create('fr_FR');

        $villes = Array();
        for ($i = 0; $i < 20; $i++) {
            $villes[$i] = new Ville();
            $villes[$i]->setNom($faker->city);
            $villes[$i]->setCodePostal(intval($faker->postcode));
            $manager->persist($villes[$i]);

        }

        $bruz = new Ville();
        $bruz->setNom('Bruz');
        $bruz->setCodePostal('35170');
        $manager->persist($bruz);
        $this->addReference('ville-bruz', $bruz);

        $pace = new Ville();
        $pace->setNom('Pacé');
        $pace->setCodePostal('35750');
        $manager->persist($pace);
        $this->addReference('ville-pace', $pace);



        $montgermont = new Ville();
        $montgermont->setNom('Montgermont');
        $montgermont->setCodePostal('35760');
        $manager->persist($montgermont);
        $this->addReference('ville-montgermont', $montgermont);




        $manager->flush();
    }
}
