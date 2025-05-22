<?php

namespace App\Repository;

use App\Entity\Vehicule;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Vehicule>
 */
class VehiculeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Vehicule::class);
    }


    public function findByUser(string $userId): array 
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.user = :val OR IDENTITY(v.user) = :val')
            ->setParameter('val', $userId)
            ->getQuery()
            ->getResult()
        ;
    }
}
