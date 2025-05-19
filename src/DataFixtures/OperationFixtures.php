<?php

namespace App\DataFixtures;

use App\Entity\Operation;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use League\Csv\Reader;
use Symfony\Component\HttpKernel\KernelInterface;

class OperationFixtures extends Fixture
{
    private $kernel;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    public function load(ObjectManager $manager): void
    {
        $csvPath = $this->kernel->getProjectDir() . '/data/operations.csv';
        $csv = Reader::createFromPath($csvPath, 'r');
        $csv->setHeaderOffset(0);
        $csv->setDelimiter(';'); // Spécifiez le délimiteur ici

        foreach ($csv as $record) {
            $operation = new Operation();
            $operation->setName($record['operation_name']);
            $operation->setAdditionnalHelp($record['additionnal_help']);
            $operation->setAdditionnalComment($record['additionnal_comment']);
            $operation->setTimeUnit((int)$record['time_unit']);
            $operation->setPrice($record['price']);
            $operation->setCreatedAt(new \DateTimeImmutable());
            $operation->setUpdatedAt(new \DateTimeImmutable());

            $manager->persist($operation);
        }

        $manager->flush();
    }
}
