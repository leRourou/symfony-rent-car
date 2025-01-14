<?php

namespace App\Service;

use App\Repository\CarRepository;

class CarService
{
    private CarRepository $carRepository;
    public function __construct(CarRepository $carRepository)
    {
        $this->carRepository = $carRepository;
    }

    public function getAllCars()
    {
        return $this->carRepository->findAllCars();
    }

    public function getCarById($id)
    {
        return $this->carRepository->findCarById($id);
    }
}
