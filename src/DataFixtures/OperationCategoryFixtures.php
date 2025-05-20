<?php

namespace App\DataFixtures;

use App\Entity\OperationCategory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class OperationCategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $categories = [
            'Je souhaite entretenir mon véhicule',
            'Je souhaite changer ou réparer mes roues ou pneus',
            'Je souhaite réaliser mon contrôle technique',
            'Je souhaite réparer mon véhicule',
            'Je souhaite entretenir ma climatisation et/ou changer mes essuie-glaces',
            'Je ne sais pas ce que je veux faire',
            'Je souhaite profiter des offres promotionnelles',
        ];

        foreach ($categories as $name) {
            $category = new OperationCategory();
            $category->setName($name);
            $manager->persist($category);
        }

        $manager->flush();
    }
}
