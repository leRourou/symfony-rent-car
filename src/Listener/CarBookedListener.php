<?php

namespace App\Listener;

use App\Event\CarBookedEvent;
use App\Service\MailService;
use App\template\mails\test;

class CarBookedListener
{
    private MailService $mailService;

    public function __construct(MailService $mailService)
    {
        $this->mailService = $mailService;
    }

    public function onCarBooked(CarBookedEvent $event): void
    {
        $details = $event->getBookingDetails();
        $to = 'jpoulain58@gmail.com';
        $subject = 'Bienvenue à bord !';
        $template = 'mails/test.html.twig';
        $context = [
            'name' => 'Jean Dupont',
        ];
        $this->mailService->sendEmail($to, $subject, $template, $context);
    }
}
