<?php

namespace App\DataFixtures;

use App\Service\FakerService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;

class UserFixtures extends Fixture
{
    private FakerService $fakerService;

    public function __construct(FakerService $fakerService)
    {
        $this->fakerService = $fakerService;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = $this->fakerService->getFaker();

        for ($i = 0; $i < 100; $i++) {
            $user = new User();

            $user
                ->setFirstname($faker->firstName())
                ->setLastname($faker->lastName())
                ->setEmail($faker->email())
                ->setPassword(password_hash('password', PASSWORD_BCRYPT));

            $manager->persist($user);
        }

        $manager->flush();
    }
}
