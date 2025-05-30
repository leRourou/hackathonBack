<?php

namespace App\Repository;

use App\Entity\Garage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Garage>
 */
class GarageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Garage::class);
    }


    public function findNearestGarages(float $lat, float $lng, int $page = 1): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $limit = 5;
        $offset = max(0, ($page - 1) * $limit);

        $sql = "
        SELECT *, 
            (6371 * ACOS(
                COS(RADIANS(:lat)) * COS(RADIANS(latitude)) * COS(RADIANS(longitude) - RADIANS(:lng)) +
                SIN(RADIANS(:lat)) * SIN(RADIANS(latitude))
            )) AS distance
        FROM garage
        ORDER BY distance ASC
        LIMIT " . intval($limit) . " OFFSET " . intval($offset);

        $stmt = $conn->prepare($sql);
        $result = $stmt->executeQuery([
            'lat' => $lat,
            'lng' => $lng
        ]);

        return $result->fetchAllAssociative();
    }
}
