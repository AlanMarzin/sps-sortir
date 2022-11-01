<?php

namespace App\Controller;

use App\Form\ProfileTypeForm;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

 #[Route("/user")]
class UserController extends AbstractController
{
    #[Route('/{id}',  name: 'user_details', requirements: ['id' => '\d+'],)]
    public function detail(Request $request, int $id, UserRepository $userRepository): Response
    {
        $user = $userRepository->find($id);
        $profileForm = $this->createForm(ProfileTypeForm::class, $user);
        $profileForm->handleRequest($request);


        if ($user === null) {
            throw $this->createNotFoundException('Page not found');
        }

        return $this->render('profile/gestionprofile.html.twig', [
            'id' => $id,
            'user' => $user,
            'profileForm' => $profileForm->createView()
        ]);
    }
}
