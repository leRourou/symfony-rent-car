<?php

namespace App\Event;

use Symfony\Contracts\EventDispatcher\Event;

class CarBookedEvent extends Event
{
    public const NAME = 'car.booked';

    private $bookingDetails;

    public function __construct(array $bookingDetails)
    {
        $this->bookingDetails = $bookingDetails;
    }

    public function getBookingDetails(): array
    {
        return $this->bookingDetails;
    }
}
