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
        $page = 1,
        $searchTerm = null
    ) {
        $qb = $this->createQueryBuilder('c');

        if ($searchTerm) {
            $qb->join('c.model', 'm')
                ->where('c.registrationNumber LIKE :searchTerm')
                ->orWhere('m.name LIKE :searchTerm')
                ->orWhere('m.brand LIKE :searchTerm')
                ->setParameter('searchTerm', '%' . $searchTerm . '%');
        }

        $offset = ($page - 1) * $limit;

        $data = $qb->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getQuery()
            ->getResult();

        $count = 0;
        if (count($data) > 0) {
            $count = $qb->select('COUNT(c.id)')
                ->setFirstResult(null)
                ->setMaxResults(null)
                ->getQuery()
                ->getSingleScalarResult();
        }


        return [
            'data' => $data,
            'count' => $count
        ];
    }

    public function findAllCars()
    {
        return $this->createQueryBuilder('c')
            ->orderBy('c.id', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
