<?php

namespace App\DataFixtures;

use App\Entity\Operation;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use League\Csv\Reader;
use Symfony\Component\HttpKernel\KernelInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Entity\OperationCategory;

class OperationFixtures extends Fixture implements DependentFixtureInterface
{
    private $kernel;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    public function getDependencies(): array
    {
        return [
            OperationCategoryFixtures::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        $csvPath = $this->kernel->getProjectDir() . '/data/operations.csv';
        $csv = Reader::createFromPath($csvPath, 'r');
        $csv->setHeaderOffset(0);
        $csv->setDelimiter(';'); // Spécifiez le délimiteur ici

        foreach ($csv as $record) {
            $operation = new Operation();

            $categoryName = $record['category'];
            /** @var OperationCategory|null $category */
            $category = $manager->getRepository(OperationCategory::class)->findOneBy(['name' => $categoryName]);

            if (!$category) {
                throw new \Exception("Category '{$categoryName}' not found.");
            }

            $operation->setCategory($category); // ← méthode à adapter selon ton entité
            $operation->setName($record['operation_name']);
            $operation->setAdditionnalHelp($record['additionnal_help'] ?: null);
            $operation->setAdditionnalComment($record['additionnal_comment'] ?: null);
            $operation->setTimeUnit((int)($record['time_unit'] ?: 0));
            $operation->setPrice((float)$record['price']);

            $manager->persist($operation);
        }


        $manager->flush();
    }
}
