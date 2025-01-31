<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Repository\UserRepository;
use App\Service\UserService;
use Symfony\Component\HttpFoundation\Request;

#[Route('/admin/users')]
#[IsGranted('ROLE_ADMIN')]
class AdminUserController extends AbstractController
{
    #[Route('/', name: 'admin_users', methods: ['GET'])]
    public function users(Request $request, UserRepository $userRepository): Response
    {
        $itemsCount = $request->query->get('itemsCount', default: 10);
        $page = $request->query->get('page', 1);
        $searchTerm = $request->query->get('search', null);
        $selected = $request->query->get('selected', default: 0);

        $users = $userRepository->search($itemsCount, $page, $searchTerm);
        $selectedUser = $userRepository->find($selected);
        $maxPage = ceil($users['count'] / $itemsCount);

        return $this->render('admin/users.html.twig', [
            'users' => $users['data'],
            'selectedUser' => $selectedUser,
            'maxPage' => $maxPage,
            'itemsCount' => $itemsCount
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_user_delete', methods: ['POST'])]
    public function deleteUser($id, UserService $userService): Response
    {
        try {
            $userService->deleteUser($id);
            $this->addFlash('success', 'Utilisateur supprimé');
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('admin_users');
    }

    #[Route('/{id}/edit', name: 'admin_user_edit', methods: ['POST'])]
    public function editUser(Request $request, $id, UserService $userService): Response
    {
        $firstname = $request->request->get('firstname');
        $lastname = $request->request->get('lastname');
        $email = $request->request->get('email');
    
        try {
            $userService->updateUser($id, $firstname, $lastname, $email);
            $this->addFlash('success', 'Utilisateur modifié');
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }
    
        return $this->redirectToRoute('admin_users');
    }
    
}
