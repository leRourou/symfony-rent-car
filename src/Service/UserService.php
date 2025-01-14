<?php

namespace App\Service;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;


class UserService
{
    private UserRepository $userRepository;
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
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
}
