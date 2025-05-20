<?php

namespace App\DataFixtures;

use App\Entity\Garage;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use League\Csv\Reader;
use Symfony\Component\HttpKernel\KernelInterface;

class GarageFixturesPhp extends Fixture
{
    private $kernel;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    public function load(ObjectManager $manager): void
    {
        $csvPath = $this->kernel->getProjectDir() . '/data/concessions.csv';
        $csv = Reader::createFromPath($csvPath, 'r');
        $csv->setHeaderOffset(0);
        $csv->setDelimiter(';');

        foreach ($csv as $record) {
            $garage = new Garage();

            $garage->setName($record['name']);
            $garage->setCity($record['city']);
            $garage->setPostalCode($record['postal_code']);
            $garage->setLatitude((float)$record['latitude']);
            $garage->setLongitude((float)$record['longitude']);

            $manager->persist($garage);
        }


        $manager->flush();
    }
}
