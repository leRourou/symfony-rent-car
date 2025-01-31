<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Repository\ReservationRepository;
use App\Service\ReservationService;
use Symfony\Component\HttpFoundation\Request;

#[Route('/admin/reservations')]
#[IsGranted('ROLE_ADMIN')]
class AdminReservationController extends AbstractController
{

    #[Route('/', name: 'admin_reservations', methods: ['GET'])]
    public function reservations(Request $request, ReservationRepository $reservationRepository): Response
    {
        $itemsCount = $request->query->get('itemsCount', default: 10);
        $page = $request->query->get('page', 1);
        $selected = $request->query->get('selected', default: 0);

        $reservations = $reservationRepository->search($itemsCount, $page);
        $selectedReservation = $reservationRepository->find($selected);
        $maxPage = ceil($reservations['count'] / $itemsCount);

        return $this->render('admin/reservations.html.twig', [
            'reservations' => $reservations['data'],
            'selectedReservation' => $selectedReservation,
            'maxPage' => $maxPage,
            'itemsCount' => $itemsCount
        ]);
    }

    #[Route('/{id}/confirm', name: 'admin_reservation_confirm', methods: ['POST'])]
    public function confirmReservation($id, Request $request, ReservationService $reservationService): Response
    {
        $action = $request->request->get('action');

        try {
            if ($action === 'confirm') {
                $reservationService->confirmReservation($id);
                $this->addFlash('success', 'Réservation confirmée');
            } else if ($action === 'cancel') {
                $reservationService->cancelReservation($id);
                $this->addFlash('success', 'Réservation annulée');
            }
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('admin_reservations', [
            'selected' => $id
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_reservation_edit', methods: ['POST'])]
    public function editReservation(Request $request, $id, ReservationService $reservationService): Response
    {
        $status = $request->request->get('status');
        $beginningDate = $request->request->get('beginningDate');
        $endingDate = $request->request->get('endingDate');

        try {
            $reservationService->updateReservation($id, $status, $beginningDate, $endingDate);
            $this->addFlash('success', 'Réservation modifiée');
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('admin_reservations', [
            'selected' => $id
        ]);
    }

}
