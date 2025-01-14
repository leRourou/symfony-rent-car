<?php

namespace App\Controller;

use App\Service\CarService;
use App\Service\ReservationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;

class CarController extends AbstractController
{
    #[Route('/cars', name: 'app_car')]
    public function index(CarService $carService)
    {
        $cars = $carService->getAllCars();
        return $this->json($cars);
    }

    #[Route('/cars/{id}', name: 'app_car_show')]
    public function show(CarService $carService, int $id)
    {
        $car = $carService->getCarById($id);
        return $this->json($car);
    }

    #[Route('/cars/{id}/availability', name: 'app_car_availability')]
    public function availability(
        Request $request,
        SerializerInterface $serializer,
        ReservationService $reservationService,
        int $id
    ) {
        $month = $request->query->getInt('month');
        $year = $request->query->getInt('year');

        if (!$month || !$year) {
            return new JsonResponse('Month and year are required', 400);
        }

        $number_of_days = $reservationService->getNumberOfDays($month, $year);
        $month_name = $reservationService->getMonthName($month);
        $reserved = $reservationService->getReservedDaysForAMonth($id, $month, $year);

        $data = $serializer->serialize([
            'month' => $month_name,
            'reserved' => $reserved,
            'number_of_days' => $number_of_days,
        ], 'json');

        return new JsonResponse($data, 200, [], true);
    }
}
