<?php

namespace App\Service;

use App\Entity\ReservationStatus;
use App\Repository\ReservationRepository;

class ReservationService
{
    private ReservationRepository $reservationRepository;
    public function __construct(ReservationRepository $reservationRepository)
    {
        $this->reservationRepository = $reservationRepository;
    }

    public function getReservedDaysForAMonth($id, $month, $year)
    {
        $reservations = $this->reservationRepository->findReservedDays($id, $month, $year);
        $reserved = [];
        for ($i = 0; $i < count($reservations); $i++) {
            $reservation = $reservations[$i];
            $beginningDate = $reservation->getBeginningDate();
            $endingDate = $reservation->getEndingDate();
            $interval = new \DateInterval('P1D');
            $dateRange = new \DatePeriod($beginningDate, $interval, $endingDate);
            foreach ($dateRange as $date) {
                $reserved[] = $date->format('d');
            }
        }
        return $reserved;
    }
    public function getNumberOfDays($month, $year)
    {
        return cal_days_in_month(CAL_GREGORIAN, $month, $year);
    }

    public function getMonthName($month)
    {
        setlocale(LC_TIME, 'fr_FR.UTF-8');
        $formatter = new \IntlDateFormatter('fr_FR', \IntlDateFormatter::FULL, \IntlDateFormatter::NONE, null, null, 'MMMM');
        return ucfirst($formatter->format(mktime(0, 0, 0, $month, 10)));
    }

    public function getReservationsByUser($user)
    {
        $reservations = $this->reservationRepository->findBy(['user' => $user]);
        foreach ($reservations as $reservation) {
        }
        return $reservations;
    }

    public function getAllReservedDays(int $carId): array
    {
        $reservations = $this->reservationRepository->findBy(['car' => $carId]);
        $reservedDates = [];

        foreach ($reservations as $reservation) {
            $beginningDate = $reservation->getBeginningDate();
            $endingDate = $reservation->getEndingDate();
            $interval = new \DateInterval('P1D');
            $dateRange = new \DatePeriod($beginningDate, $interval, $endingDate->modify('+1 day'));

            foreach ($dateRange as $date) {
                $reservedDates[] = $date->format('Y-m-d');
            }
        }

        return array_values(array_unique($reservedDates));
    }

    public function cancelReservation($id)
    {
        $reservation = $this->reservationRepository->find($id);

        if (!$reservation) {
            throw new \Exception('Réservation introuvable');
        }


        if ($reservation->getStatus() === 'canceled') {
            throw new \Exception('Réservation déjà annulée');
        }

        $reservation->setStatus(ReservationStatus::Canceled);
        $this->reservationRepository->save($reservation);
    }

    public function confirmReservation($id)
    {
        $reservation = $this->reservationRepository->find($id);

        if (!$reservation) {
            throw new \Exception('Réservation introuvable');
        }

        if ($reservation->getStatus() === 'confirmed') {
            throw new \Exception('Réservation déjà confirmée');
        }

        $reservation->setStatus(ReservationStatus::Confirmed);
        $this->reservationRepository->save($reservation);
    }
}
