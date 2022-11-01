<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $hasher)
    {
    }

    public function load(ObjectManager $manager): void
    {

//        $axelle = new User();
//        $axelle->setPseudo('axelle');
//        $axelle->setEmail('axelle.cardin@gmail.com');
//        $axelle->setPassword($this->hasher->hashPassword($axelle, 'axelle'));
//        $manager->persist($axelle);
//
//        $fred = new User();
//        $fred->setPseudo('fred');
//        $fred->setEmail('fred.arthaud@gmail.com');
//        $fred->setPassword($this->hasher->hashPassword($fred, 'fred'));
//        $manager->persist($fred);
//
//        $alan = new User();
//        $alan->setPseudo('alan');
//        $alan->setEmail('alan.marzin@gmail.com');
//        $alan->setPassword($this->hasher->hashPassword($alan, 'alan'));
//        $manager->persist($alan);
//
//
//        $manager->flush();

    }

}
