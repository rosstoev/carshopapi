<?php
declare(strict_types=1);

namespace App\Controller;


use App\Entity\Car;
use App\Entity\Color;
use App\Entity\Image;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Vich\UploaderBundle\Storage\StorageInterface;

class CarCreatorController extends AbstractController
{
    public function __invoke(Request $request)
    {
        $car = new Car();
        $car->setBrand($request->get('brand'));
        $car->setModel($request->get('model'));

        $colorName = $request->get('color');
        if (null != $colorName) {
            $color = new Color();
            $color->setName($colorName);
            $car->setColor($color);
        }

        $uploadedFile = $request->files->get('image');

        if (null !== $uploadedFile) {
            $image = new Image();
            $image->setImageFile($uploadedFile);
            $image->setUpdateAt(new \DateTime());
            $car->setImage($image);
        }

        return $car;
    }
}