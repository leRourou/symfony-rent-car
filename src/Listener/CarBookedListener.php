<?php

namespace App\EventListener;

use App\Event\CarBookedEvent;
use App\Service\MailService;

class CarBookedListener
{

    private $mailer;
    public function __construct(MailService $mailService)
    {
        $this->mailService = $mailService;
    }
    public function onCarBooked(CarBookedEvent $event): void
    {
        $details = $event->getBookingDetails();
        echo "Réservation enregistrée pour la voiture ID: " . $details['carId'];
    }
}
