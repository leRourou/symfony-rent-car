<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTime $beginningDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTime $endingDate = null;

    #[ORM\Column(enumType: ReservationStatus::class)]
    private ?ReservationStatus $status = null;

    #[ORM\ManyToOne]
    private ?User $user = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Car $car = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBeginningDate(): ?\DateTime
    {
        return $this->beginningDate;
    }

    public function setBeginningDate(\DateTime $beginning_date): static
    {
        $this->beginningDate = $beginning_date;

        return $this;
    }

    public function getEndingDate(): ?\DateTime
    {
        return $this->endingDate;
    }

    public function setEndingDate(\DateTime $ending_date): static
    {
        $this->endingDate = $ending_date;

        return $this;
    }

    public function getStatus(): ?ReservationStatus
    {
        return $this->status;
    }

    public function setStatus(ReservationStatus $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getCar(): ?Car
    {
        return $this->car;
    }

    public function setCar(?Car $car): static
    {
        $this->car = $car;

        return $this;
    }

    public function getDaysDuration(): int
    {
        return $this->beginningDate->diff($this->endingDate)->days;
    }
}
