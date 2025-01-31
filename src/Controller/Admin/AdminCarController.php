<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Repository\CarRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Form\EditCarType;
use Doctrine\ORM\EntityManagerInterface;

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

    #[Route('/edit/{id}', name: 'admin_car_edit', methods: ['GET', 'POST'])]
    public function editCar(int $id, Request $request, CarRepository $carRepository, EntityManagerInterface $entityManager): Response
    {
        $car = $carRepository->find($id);

        if (!$car) {
            throw $this->createNotFoundException('Véhicule non trouvé.');
        }

        $form = $this->createForm(EditCarType::class, $car);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($car);
            $entityManager->flush();

            $this->addFlash('success', 'Le véhicule a été mis à jour avec succès.');
            return $this->redirectToRoute('admin_cars');
        } else {
            return $this->render('admin/edit_car.html.twig', [
                'form' => $form->createView(),
                'car' => $car,
            ]);
        }
    }

    #[Route('/delete/{id}', name: 'admin_car_delete', methods: ['GET'])]
    public function deleteCar(int $id, CarRepository $carRepository, EntityManagerInterface $entityManager): Response
    {
        $car = $carRepository->find($id);

        if (!$car) {
            throw $this->createNotFoundException('Véhicule non trouvé.');
        }

        $entityManager->remove(object: $car);
        $entityManager->flush();

        $this->addFlash('success', 'Le véhicule a été supprimé avec succès.');
        return $this->redirectToRoute('admin_cars');
    }
}
