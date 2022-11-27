<?php

namespace App\Controller;

use App\Form\RegisterType;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;

class RegisterController extends AbstractController
{
    // Au choix dans ton constructeur ou dans chaque méthode :
    public function __construct(private ManagerRegistry $doctrine) {}

    #[Route('/inscription', name: 'app_register')]
    public function index(Request $request, UserPasswordHasherInterface $hasher): Response
    {

        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);

        $form->handleRequest($request);

        if ( $form-> isSubmitted() && $form -> isValid()) {
            $user = $form->getData();

            $password = $hasher->hashPassword($user,$user->getPassword());

            $user->setPassword($password);

            $em = $this -> doctrine ->getManager();
            $em ->persist($user);
            $em -> flush();

        }

        return $this->render('register/index.html.twig', [
            'form' =>  $form->createView()
        ]);
    }
}
