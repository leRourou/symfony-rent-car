<?php

namespace App\Service;

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
}
