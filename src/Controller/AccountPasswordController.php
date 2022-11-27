<?php

namespace App\Controller;

use App\Form\ChangePasswordType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class AccountPasswordController extends AbstractController
{

    // Au choix dans ton constructeur ou dans chaque méthode :
    public function __construct(private ManagerRegistry $doctrine) {}

    #[Route('/account/change_password', name: 'app_account_password')]
    public function index(Request $request, UserPasswordHasherInterface $hasher): Response
    {

        $notification = null;

        $user = $this->getUser();
        $form = $this->createForm(ChangePasswordType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $old_pwd = $form->get('old_password')->getData();

            if($hasher ->isPasswordValid($user, $old_pwd)){
                $new_pwd = $form->get('new_password')->getData();
                $password = $hasher->hashPassword($user, $new_pwd);

                $user->setPassword($password);
                $em = $this -> doctrine ->getManager();
                $em ->persist($user);
                $em -> flush();
                $notification = "Votre mot de passe a bien été mis à jour.";

            } else{
                $notification = "Votre mot de passe actuel n'est pas le bon";
            }

        }

        return $this->render('account/password.html.twig', [
            'form' => $form->createView(),
            'notification'=>$notification
        ]);

    }
}
