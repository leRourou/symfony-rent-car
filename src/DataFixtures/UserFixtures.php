<?php

namespace App\DataFixtures;

use App\Service\FakerService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private FakerService $fakerService;
    private UserPasswordHasherInterface $hasher;

    public function __construct(FakerService $fakerService, UserPasswordHasherInterface $hasher)
    {
        $this->fakerService = $fakerService;
        $this->hasher = $hasher;
    }

    private function addAdmin(ObjectManager $manager): void
    {
        $admin = new User();
        $admin
            ->setFirstname('Admin')
            ->setLastname('Admin')
            ->setEmail('admin@car-rental.fr')
            ->setPassword($this->hasher->hashPassword($admin, 'admin'))
            ->setRoles(['ROLE_ADMIN']);

        $manager->persist($admin);
    }

    public function load(ObjectManager $manager): void
    {
        $faker = $this->fakerService->getFaker();

        $this->addAdmin($manager);

        # Create 99 sample users
        for ($i = 0; $i < 99; $i++) {
            $user = new User();

            $user
                ->setFirstname($faker->firstName())
                ->setLastname($faker->lastName())
                ->setEmail($faker->email())
                ->setPassword($this->hasher->hashPassword($user, $faker->password()));

            $this->addReference('user_' . $i, object: $user);
            $manager->persist($user);
        }

        $manager->flush();
    }
}
