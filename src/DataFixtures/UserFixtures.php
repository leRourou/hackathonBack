<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture {

    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher)
    { }
    
    public function load(ObjectManager $manager): void
    {
        
        $adminUser = new User();

        $adminUser->setFirstName('Racoon');
        $adminUser->setLastName('Bosster');

        $adminUser->setPhone('0606060606');
        $adminUser->setIsDriver(false);

        $adminUser->setEmail('racoon@admin.fr');
        
        $hashedPassword = $this->passwordHasher->hashPassword(
            $adminUser,
            'racoonadmin'
        );
        $adminUser->setPassword($hashedPassword);

        $adminUser->setRoles(['ROLE_ADMIN']);


        $manager->persist($adminUser);

        $manager->flush();
    }
} 

