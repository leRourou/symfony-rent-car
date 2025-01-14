<?php

namespace App\DataFixtures;

use App\Entity\CarModel;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CarModelFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        $models = [
            // Peugeot
            'Peugeot 206',
            'Peugeot 207',
            'Peugeot 208',
            'Peugeot 308',
            'Peugeot 3008',

            // Toyota
            'Toyota Corolla',
            'Toyota Yaris',
            'Toyota Camry',
            'Toyota RAV4',
            'Toyota Hilux',

            // Renault
            'Renault Clio',
            'Renault Mégane',
            'Renault Captur',
            'Renault Kadjar',
            'Renault Zoe',

            // Volkswagen
            'Volkswagen Golf',
            'Volkswagen Passat',
            'Volkswagen Tiguan',
            'Volkswagen Polo',
            'Volkswagen ID.4',

            // BMW
            'BMW Série 1',
            'BMW Série 3',
            'BMW Série 5',
            'BMW X1',
            'BMW X3',

            // Audi
            'Audi A3',
            'Audi A4',
            'Audi A6',
            'Audi Q3',
            'Audi Q5',
        ];

        for ($i = 0; $i < count($models); ++$i) {
            $model = new CarModel();

            $model->setName($models[$i]);

            $manager->persist($model);
            $this->addReference('model_' . $i, $model);
        }

        $manager->flush();
    }
}
