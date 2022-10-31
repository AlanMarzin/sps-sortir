<?php

namespace App\DataFixtures;

use App\Entity\Etat;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class EtatFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $creation = new Etat();
        $creation->setLibelle('en création');
        $manager->persist($creation);
        $this->addReference('etat-creation', $creation);

        $ouverte = new Etat();
        $ouverte->setLibelle('ouverte');
        $manager->persist($ouverte);
        $this->addReference('etat-ouverte', $ouverte);

        $cloturee = new Etat();
        $cloturee->setLibelle('clôturée');
        $manager->persist($cloturee);
        $this->addReference('etat-cloturee', $cloturee);

        $encours = new Etat();
        $encours->setLibelle('en cours');
        $manager->persist($encours);
        $this->addReference('etat-encours', $encours);

        $terminee = new Etat();
        $terminee->setLibelle('terminée');
        $manager->persist($terminee);
        $this->addReference('etat-terminee', $terminee);

        $annulee = new Etat();
        $annulee->setLibelle('annulée');
        $manager->persist($annulee);
        $this->addReference('etat-annulee', $annulee);

        $historisee = new Etat();
        $historisee->setLibelle('historisée');
        $manager->persist($historisee);
        $this->addReference('etat-historisee', $historisee);

        $manager->flush();
    }

}
