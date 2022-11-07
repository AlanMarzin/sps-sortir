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
        $this->addReference('campus-rennes', $rennes);

        $nantes = new Campus();
        $nantes->setNom('NANTES');
        $manager->persist($nantes);
        $this->addReference('campus-nantes', $nantes);

        $quimper = new Campus();
        $quimper->setNom('QUIMPER');
        $manager->persist($quimper);
        $this->addReference('campus-quimper', $quimper);

        $niort = new Campus();
        $niort->setNom('NIORT');
        $manager->persist($niort);
        $this->addReference('campus-niort', $niort);

        $manager->flush();
    }
}
