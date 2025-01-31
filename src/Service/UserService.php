<?php

namespace App\Service;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class UserService
{
    private UserRepository $userRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(UserRepository $userRepository, EntityManagerInterface $entityManager)
    {
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
    }

    public function deleteUser($id)
    {
        $user = $this->userRepository->find($id);

        if (!$user) {
            throw new \Exception('Utilisateur non trouvé');
        }

        if ($user->isAdmin()) {
            throw new \Exception('Vous ne pouvez pas supprimer un administrateur');
        }

        if ($user->getReservations()->count() > 0) {
            throw new \Exception('Vous ne pouvez pas supprimer un utilisateur avec des réservations');
        }

        $this->userRepository->delete($user);
    }

    public function updateUser($id, $firstname, $lastname, $email)
    {
        $user = $this->userRepository->find($id);

        if (!$user) {
            throw new \Exception('Utilisateur non trouvé');
        }

        $user->setFirstname($firstname);
        $user->setLastname($lastname);
        $user->setEmail($email);

        $this->entityManager->flush();
    }
}
