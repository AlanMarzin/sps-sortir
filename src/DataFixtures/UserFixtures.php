<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture implements  DependentFixtureInterface
{
    public function __construct(private UserPasswordHasherInterface $hasher)
    {
    }

    public function load(ObjectManager $manager): void
    {

        $axelle = new User();
        $axelle->setPseudo('axelle');
        $axelle->setEmail('axelle.cardin@gmail.com');
        $axelle->setPassword($this->hasher->hashPassword($axelle, 'axelle'));
        $axelle->setCampus($this->getReference('campus-rennes'));
        $axelle->setRoles(['ROLE_USER']);
        $axelle->setActif(true);
        $axelle->setTelephone('0123456789');
        $axelle->setNom('CARDIN');
        $axelle->setPrenom('Axelle');
        $manager->persist($axelle);
        $this->addReference('user-axelle', $axelle);

        $fred = new User();
        $fred->setPseudo('fred');
        $fred->setEmail('fred.arthaud@gmail.com');
        $fred->setPassword($this->hasher->hashPassword($fred, 'fred'));
        $fred->setCampus($this->getReference('campus-rennes'));
        $fred->setRoles(['ROLE_USER']);
        $fred->setActif(true);
        $fred->setTelephone('0123456789');
        $fred->setNom('ARTHAUD');
        $fred->setPrenom('Fred');
        $manager->persist($fred);
        $this->addReference('user-fred', $fred);

        $alan = new User();
        $alan->setPseudo('alan');
        $alan->setEmail('alan.marzin@gmail.com');
        $alan->setPassword($this->hasher->hashPassword($alan, 'alan'));
        $alan->setCampus($this->getReference('campus-rennes'));
        $alan->setRoles(['ROLE_USER']);
        $alan->setActif(true);
        $alan->setTelephone('0123456789');
        $alan->setNom('MARZIN');
        $alan->setPrenom('Alan');
        $manager->persist($alan);
        $this->addReference('user-alan', $alan);

        $manager->flush();

    }

    public function getDependencies()
    {
        return [CampusFixtures::class];
    }
}
