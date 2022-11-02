<?php

namespace App\DataFixtures;

use App\Entity\Lieu;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class LieuFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
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
