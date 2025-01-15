<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Repository\CarRepository;
use Symfony\Component\HttpFoundation\Request;

#[Route('/admin/cars')]
#[IsGranted('ROLE_ADMIN')]
class AdminCarController extends AbstractController
{
    #[Route('/', name: 'admin_cars', methods: ['GET'])]
    public function cars(Request $request, CarRepository $carRepository): Response
    {
        $itemsCount = $request->query->get('itemsCount', default: 10);
        $page = $request->query->get('page', 1);
        $searchTerm = $request->query->get('search', null);
        $selected = $request->query->get('selected', default: 0);

        $cars = $carRepository->search($itemsCount, $page, $searchTerm);
        $selectedCar = $carRepository->find($selected);
        $maxPage = ceil($cars['count'] / $itemsCount);

        return $this->render('admin/cars.html.twig', [
            'cars' => $cars['data'],
            'selectedCar' => $selectedCar,
            'maxPage' => $maxPage,
            'itemsCount' => $itemsCount
        ]);
    }
}
