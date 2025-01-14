<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Entity\ReservationStatus;
use App\Form\ReservationType;
use App\Service\CarService;
use App\Service\ReservationService;
use App\Repository\CarRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReservationController extends AbstractController
{
    #[Route('/reserve', name: 'app_reserve')]
    public function reservePage(CarService $carService, ReservationService $reservationService): Response
    {
        $cars = $carService->getAllCars();
        $currentMonth = (int)(new \DateTime())->format('m');
        $currentYear = (int)(new \DateTime())->format('Y');
    
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
            $dates = $request->request->get('reservation');
            
            if ($form->isSubmitted() && $form->isValid()) {
                $dates = $request->request->get('reservation');
                
                if (!empty($dates['beginningDate']) && !empty($dates['endingDate'])) {
                    try {
                        $beginningDate = new \DateTime($dates['beginningDate']);
                        $endingDate = new \DateTime($dates['endingDate']);
                        $reservation->setBeginningDate($beginningDate);
                        $reservation->setEndingDate($endingDate);
            
                        $entityManager->persist($reservation);
                        $entityManager->flush();
            
                        $this->addFlash('success', 'Réservation réussie !');
                        return $this->redirectToRoute('app_reserve');
                    } catch (\Exception $e) {
                        $this->addFlash('error', 'Les dates fournies sont invalides.');
                    }
                } else {
                    $this->addFlash('error', 'Les dates sont obligatoires.');
                }
            }
        }

        return $this->render('reservation/reserve.html.twig', [
            'car' => $car,
            'form' => $form->createView(),
        ]);
    }
}