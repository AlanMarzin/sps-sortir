<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker;

class UserFixtures extends Fixture implements  DependentFixtureInterface
{
    public function __construct(private UserPasswordHasherInterface $hasher)
    {
    }

    public function load(ObjectManager $manager): void
    {

        $faker = Faker\Factory::create('fr_FR');
        $campus = $manager->getRepository(Campus::class)->findAll();

        $user = Array();
        for ($i = 0; $i < 50; $i++) {
            $user[$i] = new User();
            $user[$i]->setPseudo($faker->userName);
            $user[$i]->setEmail($faker->email);
            $user[$i]->setPassword($this->hasher->hashPassword($user[$i], $faker->colorName));
            $user[$i]->setCampus($faker->randomElement($campus));
            $user[$i]->setRoles(['ROLE_USER']);
            $user[$i]->setActif(true);
            $user[$i]->setTelephone($faker->phoneNumber);
            $user[$i]->setNom($faker->lastName);
            $user[$i]->setPrenom($faker->firstName);


            $manager->persist($user[$i]);

        }


        $axelle = new User();
        $axelle->setPseudo('axelle');
        $axelle->setEmail('axelle.cardin@gmail.com');
        $axelle->setPassword($this->hasher->hashPassword($axelle, 'axelle'));
        $axelle->setCampus($this->getReference('campus-rennes'));
        $axelle->setRoles(['ROLE_ADMIN']);
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
        $fred->setRoles(['ROLE_ADMIN']);
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
        $alan->setRoles(['ROLE_ADMIN']);
        $alan->setActif(true);
        $alan->setTelephone('0123456789');
        $alan->setNom('MARZIN');
        $alan->setPrenom('Alan');
        $manager->persist($alan);
        $this->addReference('user-alan', $alan);

        $eliot = new User();
        $eliot->setPseudo('eliot');
        $eliot->setEmail('eliot@gmail.com');
        $eliot->setPassword($this->hasher->hashPassword($eliot, 'eliot'));
        $eliot->setCampus($this->getReference('campus-rennes'));
        $eliot->setRoles(['ROLE_USER']);
        $eliot->setActif(true);
        $eliot->setTelephone('0123456789');
        $eliot->setNom('GALLE');
        $eliot->setPrenom('Eliot');
        $manager->persist($eliot);
        $this->addReference('user-eliot', $eliot);

        $lyndzoua = new User();
        $lyndzoua->setPseudo('ly ndzoua');
        $lyndzoua->setEmail('lyndzoua@gmail.com');
        $lyndzoua->setPassword($this->hasher->hashPassword($lyndzoua, 'lyndzoua'));
        $lyndzoua->setCampus($this->getReference('campus-rennes'));
        $lyndzoua->setRoles(['ROLE_USER']);
        $lyndzoua->setActif(true);
        $lyndzoua->setTelephone('0123456789');
        $lyndzoua->setNom('XENEXAY');
        $lyndzoua->setPrenom('Ly Ndzoua');
        $manager->persist($lyndzoua);
        $this->addReference('user-lyndzoua', $lyndzoua);

        $manager->flush();

    }

    public function getDependencies()
    {
        return [CampusFixtures::class];
    }
}
