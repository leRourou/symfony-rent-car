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
            'Peugeot' => ['206', '207', '208', '308', '3008'],

            // Toyota
            'Toyota' => ['Corolla', 'Yaris', 'Camry', 'RAV4', 'Hilux'],

            // Renault
            'Renault' => ['Clio', 'Mégane', 'Captur', 'Kadjar', 'Zoe'],

            // Volkswagen
            'Volkswagen' => ['Golf', 'Passat', 'Tiguan', 'Polo', 'ID.4'],

            // BMW
            'BMW' => ['Série 1', 'Série 3', 'Série 5', 'X1', 'X3'],

            // Audi
            'Audi' => ['A3', 'A4', 'A6', 'Q3', 'Q5'],
        ];

        $i = 0;

        foreach ($models as $brand => $modelNames) {
            foreach ($modelNames as $modelName) {
                $model = new CarModel();
                $model->setName($modelName);
                $model->setBrand($brand);

                $manager->persist($model);
                $this->addReference('model_' . $i, $model);
                $i++;
            }
        }

        $manager->flush();
    }
}