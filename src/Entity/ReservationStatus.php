<?php

namespace App\Entity;

enum ReservationStatus: string
{
    case Pending = 'pending';
    case Confirmed = 'confirmed';
    case Canceled = 'canceled';
}
