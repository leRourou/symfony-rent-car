<?php

namespace App\Repository;

use App\Entity\Reservation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Reservation>
 */
class ReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservation::class);
    }

    public function findReservedDays(int $carId, int $month, int $year): array
    {
        $beginningDate = new \DateTimeImmutable(sprintf('%d-%d-01 00:00:00', $year, $month));
        $endingDate = $beginningDate->modify('last day of this month')->setTime(23, 59, 59);

        return $this->createQueryBuilder('r')
            ->select('r')
            ->where('r.car = :carId')
            ->andWhere('r.beginningDate BETWEEN :beginningDate AND :endingDate')
            ->orWhere('r.endingDate BETWEEN :beginningDate AND :endingDate')
            ->setParameter('carId', $carId)
            ->setParameter('beginningDate', $beginningDate)
            ->setParameter('endingDate', $endingDate)
            ->getQuery()
            ->getResult();
    }
}
