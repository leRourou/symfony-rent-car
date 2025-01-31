<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Entity\ReservationStatus;
use App\Form\ReservationType;
use App\Form\ReviewType;
use App\Entity\Review;
use App\Service\CarService;
use App\Service\ReservationService;
use App\Repository\CarRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReservationController extends AbstractController
{
    #[Route('/', name: 'app_reserve')]
    public function reservePage(CarService $carService, ReservationService $reservationService): Response
    {
        $cars = $carService->getAllCars();

        $carsWithAvailability = [];
        foreach ($cars as $car) {
            $model = $car->getModel();

            $carsWithAvailability[] = [
                'car' => $car,
                'isAvailable' => $car->isCanBeRent(),
                'modelName' => $model ? $model->getName() : 'Modèle inconnu',
                'brand' => $model ? $model->getBrand() : 'Marque inconnue',
            ];
        }

        return $this->render('reservation/index.html.twig', [
            'cars' => $carsWithAvailability,
        ]);
    }

    #[Route('/reserve/{carId}', name: 'app_reservation')]
    public function reserve(
        int $carId,
        CarRepository $carRepository,
        ReservationService $reservationService,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $car = $carRepository->find($carId);
        if (!$car) {
            throw $this->createNotFoundException('Voiture non trouvée');
        }

        $reservedDates = $reservationService->getAllReservedDays($carId);

        $reservation = new Reservation();
        $reservation->setCar($car);
        $reservation->setUser($this->getUser());
        $reservation->setStatus(ReservationStatus::Pending);

        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($reservation);
            $entityManager->flush();

            $this->addFlash('success', 'Réservation réussie !');
            return $this->redirectToRoute('app_reserve');
        }
        $reviews = $car->getReviews();

        return $this->render('reservation/reserve.html.twig', [
            'car' => $car,
            'form' => $form->createView(),
            'reservedDates' => $reservedDates,
            'reviews' => $reviews,
        ]);
    }

    #[Route('/my-reservations', name: 'app_my_reservations')]
    public function myReservations(ReservationService $reservationService): Response
    {
        $reservations = $reservationService->getReservationsByUser($this->getUser());
        $reviewForms = [];

        foreach ($reservations as $reservation) {
            $review = new Review();
            $reviewForm = $this->createForm(ReviewType::class, $review, [
                'action' => $this->generateUrl('app_reservation_review', ['id' => $reservation->getId()]),
                'method' => 'POST',
            ]);
            $reviewForms[$reservation->getId()] = $reviewForm->createView();
        }

        return $this->render('reservation/my_reservations.html.twig', [
            'reservations' => $reservations,
            'reviewForms' => $reviewForms,
        ]);
    }

    #[Route('/delete-reservation/{id}', name: 'app_delete_reservation', methods: ['POST'])]
    public function deleteReservation(Request $request, Reservation $reservation, EntityManagerInterface $em): Response
    {
        if (!$this->isCsrfTokenValid('delete_reservation_' . $reservation->getId(), $request->request->get('_token'))) {
            $this->addFlash('error', 'Jeton CSRF invalide.');
            return $this->redirectToRoute('app_my_reservations');
        }

        $em->remove($reservation);
        $em->flush();

        $this->addFlash('success', 'La réservation a été supprimée avec succès.');

        return $this->redirectToRoute('app_my_reservations');
    }
}
