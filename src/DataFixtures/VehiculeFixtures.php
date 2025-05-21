<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Entity\User;
use App\Entity\Vehicule;

class VehiculeFixtures extends Fixture implements DependentFixtureInterface
{
    private array $brandsAndModels = [
        'Peugeot' => ['208', '308', '3008', '5008'],
        'Renault' => ['Clio', 'Captur', 'Megane', 'Scenic'],
        'Citroen' => ['C3', 'C4', 'C5 Aircross'],
        'Volkswagen' => ['Golf', 'Polo', 'Tiguan'],
        'Toyota' => ['Yaris', 'Corolla', 'RAV4'],
        'Ford' => ['Fiesta', 'Focus', 'Kuga'],
        'BMW' => ['Serie 1', 'Serie 3', 'X1', 'X5'],
        'Mercedes' => ['Classe A', 'Classe C', 'GLA'],
        'Audi' => ['A1', 'A3', 'Q3'],
        'Opel' => ['Corsa', 'Astra', 'Mokka'],
    ];

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        $userRepository = $manager->getRepository(User::class);
        $users = $userRepository->findAll();

        if (empty($users)) {
            throw new \Exception('No users found. Please load UserFixtures first.');
        }

        foreach ($users as $user) {
            $vehiculeCount = $this->getRandomVehiculeCount();

            for ($i = 0; $i < $vehiculeCount; $i++) {
                [$brand, $model] = $this->getRandomBrandAndModel();

                $vehicule = new Vehicule();
                $vehicule->setUser($user);
                $vehicule->setBrand($brand);
                $vehicule->setModel($model);
                $vehicule->setLicensePlate($this->generateFrenchLicensePlate());
                $vehicule->setMileage(random_int(0, 200000));
                $vehicule->setVin($this->generateVin());
                $vehicule->setRegistrationDate($this->generateRegistrationDate());
                $manager->persist($vehicule);
            }
        }

        $manager->flush();
    }

    private function getRandomVehiculeCount(): int
    {
        return random_int(0, 5);
    }

    private function generateRegistrationDate(): \DateTime
    {
        $start = strtotime('2015-01-01');
        $end = strtotime('2025-01-01');

        $timestamp = random_int($start, $end);
        return (new \DateTime())->setTimestamp($timestamp);
    }

    private function getRandomBrandAndModel(): array
    {
        $brands = array_keys($this->brandsAndModels);
        $brand = $brands[array_rand($brands)];
        $model = $this->brandsAndModels[$brand][array_rand($this->brandsAndModels[$brand])];
        return [$brand, $model];
    }

    private function generateFrenchLicensePlate(): string
    {
        $letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $digits = '0123456789';

        $part1 = $letters[random_int(0, 25)] . $letters[random_int(0, 25)];
        $part2 = str_pad((string)random_int(0, 999), 3, '0', STR_PAD_LEFT);
        $part3 = $letters[random_int(0, 25)] . $letters[random_int(0, 25)];

        return $part1 . $part2 . $part3; // Sans tirets
    }


    private function generateVin(): string
    {
        $characters = 'ABCDEFGHJKLMNPRSTUVWXYZ0123456789'; // sans I, O, Q
        $vin = '';
        for ($i = 0; $i < 17; $i++) {
            $vin .= $characters[random_int(0, strlen($characters) - 1)];
        }
        return $vin;
    }
}
