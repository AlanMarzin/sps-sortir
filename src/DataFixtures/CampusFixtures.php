<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CampusFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $rennes = new Campus();
        $rennes->setNom('RENNES');
        $manager->persist($rennes);

        $nantes = new Campus();
        $nantes->setNom('NANTES');
        $manager->persist($nantes);

        $quimper = new Campus();
        $quimper->setNom('QUINPER');
        $manager->persist($quimper);

        $manager->flush();
    }
}
