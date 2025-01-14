<?php

namespace App\Repository;

use App\Repository\Searchable;
use App\Entity\Car;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Car>
 */
class CarRepository extends ServiceEntityRepository implements Searchable
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Car::class);
    }

    public function search(
        $limit = 10,
        $searchTerm = null
    ) {
        $qb = $this->createQueryBuilder('c');

        if ($searchTerm) {
            $qb->join('c.model', 'm')
                ->where('c.registrationNumber LIKE :searchTerm')
                ->orWhere('m.name LIKE :searchTerm')
                ->setParameter('searchTerm', '%' . $searchTerm . '%');
        }

        return $qb->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function findAllCars()
    {
        return $this->createQueryBuilder('c')
            ->orderBy('c.id', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
