<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function index(AuthenticationUtils $authentificationUtils): Response
    {
        //obtenir le login error s'il y en a un
        $error = $authentificationUtils->getLastAuthenticationError();

        //dernier username entré par l'utilisateur
        $lastUsername = $authentificationUtils->getLastUsername();

        return $this->render('login/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    #[Route('/plzlogin', name: 'app_plzlogin')]
    public function plzLogin(AuthenticationUtils $authentificationUtils): Response
    {
        return $this->render('login/plzlogin.html.twig');
    }
}

