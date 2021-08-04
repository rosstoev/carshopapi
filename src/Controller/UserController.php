<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/register/{email}/{password}/{phoneNumber}", defaults={"phoneNumber"=null}, name="register")
     */
    public function register(string $email, string $password, ?string $phoneNumber, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $em): Response
    {
        $user = new User();
        $user->setEmail($email);
        $user->setRoles(['ROLE_USER']);
        $hashPassword = $passwordHasher->hashPassword($user, $password);
        $user->setPassword($hashPassword);
        $user->setPhoneNumber($phoneNumber);
        $em->persist($user);
        $em->flush();

        return new Response(sprintf("Successfully created new User: %s", $user->getUserIdentifier()));

    }

    /**
     * @Route ("/token/{email}/{password}", name="token")
     */
    public function getToken(string $email, string $password, UserRepository $userRepo, UserPasswordHasherInterface $passwordHasher, JWTTokenManagerInterface $jwtManger)
    {
        $user = $userRepo->findOneBy(['email' => $email]);
        $valid = false;
        if (!empty($user)) {
            $valid = $passwordHasher->isPasswordValid($user, $password);
        }

        if ($valid == true) {
            $token = $jwtManger->create($user);
            $message = sprintf("The token is: %s", $token);
        } else {
            $message = "Unknown user";
        }

        return new Response($message);
    }
}