<?php

namespace App\DataFixtures;

use App\DataFixtures\CarModelFixtures;
use App\Entity\Car;
use App\Entity\CarModel;
use App\Service\FakerService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;


class CarFixtures extends Fixture implements DependentFixtureInterface
{
    private FakerService $fakerService;

    public function __construct(FakerService $fakerService)
    {
        $this->fakerService = $fakerService;
    }

    public function getDependencies(): array
    {
        return [
            CarModelFixtures::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        $faker = $this->fakerService->getFaker();

        for ($i = 0; $i < 50; $i++) {
            $car = new Car();

            $car
                ->setModel($this->getReference('model_' . rand(0, 29), CarModel::class))
                ->setRegistrationNumber($faker->regexify('[A-Z]{2}[0-9]{3}[A-Z]{2}'))
                ->setCanBeRent($faker->boolean())
                ->setYear($faker->numberBetween(2000, 2021))
                ->setPrice($faker->randomFloat(2, 10, 100));

            $this->addReference('car_' . $i, object: $car);
            $manager->persist($car);
        }

        $manager->flush();
    }
}
