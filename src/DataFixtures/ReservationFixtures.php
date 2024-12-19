<?php

namespace App\DataFixtures;

use App\DataFixtures\CarFixtures;
use App\Entity\Reservation;
use App\Entity\User;
use App\Entity\Car;
use App\Entity\ReservationStatus;
use App\Service\FakerService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ReservationFixtures extends Fixture implements DependentFixtureInterface
{
    private FakerService $fakerService;

    public function __construct(FakerService $fakerService)
    {
        $this->fakerService = $fakerService;
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            CarFixtures::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        $faker = $this->fakerService->getFaker();

        for ($i = 0; $i < 50; $i++) {
            $reservation = new Reservation();

            $beginningDate = $faker->dateTimeBetween('-6 month', '+1 month');
            $endingDate = (clone $beginningDate)->modify('+' . rand(1, 14) . ' days');

            $user = $this->getReference('user_' . rand(0, 98), User::class);

            $car = $this->getReference('car_' . rand(0, 19), Car::class);

            $reservation
                ->setBeginningDate($beginningDate)
                ->setEndingDate($endingDate)
                ->setStatus($faker->randomElement(ReservationStatus::cases()))
                ->setUser($user)
                ->setCar($car);

            $manager->persist($reservation);
        }

        $manager->flush();
    }
}
