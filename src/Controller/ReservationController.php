<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Entity\ReservationStatus;
use App\Form\ReservationType;
use App\Repository\CarRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReservationController extends AbstractController
{
    #[Route('/reserve/{carId}', name: 'app_reservation')]
    public function reserve(
        int $carId,
        CarRepository $carRepository,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $car = $carRepository->find($carId);
        if (!$car) {
            throw $this->createNotFoundException('Model non trouvé');
        }

        $reservation = new Reservation();
        $reservation->setCar($car);
        $reservation->setUser($this->getUser());
        $reservation->setStatus(ReservationStatus::Pending);
        
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $dates = $form->get('dates')->getData();
            [$beginningDate, $endingDate] = explode(' au ', $dates);
        
            $reservation->setBeginningDate(new \DateTime($beginningDate));
            $reservation->setEndingDate(new \DateTime($endingDate));
        
            $entityManager->persist($reservation);
            $entityManager->flush();
        
            $this->addFlash('success', 'Réservation réussie !');
            return $this->redirectToRoute('app_reservation', ['carId' => $carId]);
        }

        return $this->render('reservation/reserve.html.twig', [
            'car' => $car,
            'form' => $form->createView(),
        ]);
    }
}