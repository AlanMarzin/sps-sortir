<?php

namespace App\DataFixtures;

use App\Entity\Lieu;
use App\Entity\Ville;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker;

class LieuFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {

        $faker = Faker\Factory::create('fr_FR');
        $ville = $manager->getRepository(Ville::class)->findAll();

        $lieux = Array();
        for ($i = 0; $i < 40; $i++) {
            $lieux[$i] = new Lieu();
            $lieux[$i]->setNom($faker->company);
            $lieux[$i]->setRue($faker->address);
            $lieux[$i]->setLatitude($faker->latitude);
            $lieux[$i]->setLongitude($faker->longitude);
            $lieux[$i]->setVille($faker->randomElement($ville));
            $manager->persist($lieux[$i]);

        }

        $theroof = new Lieu();
        $theroof->setNom('The Roof');
        $theroof->setRue('Rue du Roof');
        $theroof->setLatitude('48.117266');
        $theroof->setLongitude('48.695266');
        $theroof->setVille($this->getReference('ville-pace'));
        $manager->persist($theroof);
        $this->addReference('lieu-roof', $theroof);

        $fitnessLand = new Lieu();
        $fitnessLand->setNom('Fitness Land');
        $fitnessLand->setRue('Rue des muscles');
        $fitnessLand->setLatitude('50.117266');
        $fitnessLand->setLongitude('55.695266');
        $fitnessLand->setVille($this->getReference('ville-pace'));
        $manager->persist($fitnessLand);
        $this->addReference('lieu-fitnessLand', $fitnessLand);

        $rocade = new Lieu();
        $rocade->setNom('Rocade');
        $rocade->setRue('Route des bikers');
        $rocade->setLatitude('62.117266');
        $rocade->setLongitude('69.695266');
        $rocade->setVille($this->getReference('ville-montgermont'));
        $manager->persist($rocade);
        $this->addReference('lieu-rocade', $rocade);

        $manager->flush();
    }

    public function getDependencies()
    {
        return [VilleFixtures::class];

    }
}
