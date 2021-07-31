<?php
declare(strict_types=1);

namespace App\DataFixtures;


use App\Entity\Car;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CarFixtures extends Fixture
{

    public function load(ObjectManager $manager)
    {
        $car1 = $this->makeFixture('Audi', 'A4', 'black', '1.6');
        $car2 = $this->makeFixture('Mercedes', 'E30', 'black', '2.4');
        $car3 = $this->makeFixture('BMW', 'i3', 'blue', '2.0');

        $manager->persist($car1);
        $manager->persist($car2);
        $manager->persist($car3);

        $manager->flush();

    }
    private function makeFixture($brand, $model, $color, $engine): Car
    {
        $car = new Car();
        $car->setBrand($brand);
        $car->setModel($model);
        $car->setColor($color);
        $car->setEngine($engine);

        return $car;
    }
}