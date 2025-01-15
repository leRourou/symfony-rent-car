<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Entity\Car;
use App\Entity\ReservationStatus;
use App\Form\ReservationType;
use App\Service\CarService;
use App\Service\ReservationService;
use App\Repository\CarRepository;
use App\Repository\ReservationRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use App\Event\CarBookedEvent;
use App\Listener\CarBookedListener;
use App\Service\MailService;
use Symfony\Component\Security\Core\Security;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use App\Event\CarBookedEvent;
use App\Listener\CarBookedListener;
use App\Service\MailService;

class ReservationController extends AbstractController
{
    private $eventDispatcher;
    private $mailService;
    public function __construct(EventDispatcherInterface $eventDispatcher, MailService $mailService)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->mailService = $mailService;
    }
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

        return $this->render('reservation/reserve.html.twig', [
            'car' => $car,
            'form' => $form->createView(),
            'reservedDates' => $reservedDates,
        ]);
    }

    #[Route('/testo', name: 'app_testo')]
    public function test()
    {
        $bookingDetails = [
            'carModel' => 'Tesla Model 3',
            'customerName' => 'Alice Dupont',
            'bookingDate' => '2025-01-15',
        ];

        $a = $this->mailService->sendEmail('arthur.le.devedec@gmail.com', 'Car booked', 'mails/test.html.twig', $bookingDetails);
        $event = new CarBookedEvent($bookingDetails);

        $this->eventDispatcher->dispatch($event, CarBookedEvent::NAME);

        return new Response('Car booked and event dispatched!');
    }

    #[Route('/my-reservations', name: 'app_my_reservations')]
    public function myReservations(ReservationService $reservationService): Response
    {
        $reservations = $reservationService->getReservationsByUser($this->getUser());
        return $this->render('reservation/my_reservations.html.twig', [
            'reservations' => $reservations,
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
    
    
    


    
    #[Route('/testo', name: 'app_testo')]
    public function test()
    {
        $bookingDetails = [
            'carModel' => 'Tesla Model 3',
            'customerName' => 'Alice Dupont',
            'bookingDate' => '2025-01-15',
        ];

        $a = $this->mailService->sendEmail('arthur.le.devedec@gmail.com', 'Car booked', 'mails/test.html.twig', $bookingDetails);
        $event = new CarBookedEvent($bookingDetails);

        $this->eventDispatcher->dispatch($event, CarBookedEvent::NAME);

        return new Response('Car booked and event dispatched!');
    }

}