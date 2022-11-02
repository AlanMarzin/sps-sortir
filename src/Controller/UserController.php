<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfileTypeForm;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
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

        if ($user === null) {
            throw $this->createNotFoundException('Page not found');
        }

        return $this->render('profile/details_profile.html.twig', [
            'id' => $id,
            'user' => $user,
        ]);
    }
    #[Route('update/{id}', name: 'profil_update', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function update(Request $request, int $id, UserRepository $userRepository, EntityManagerInterface $em, User $user): Response{
            $user = $userRepository->find($id);
            $profileForm = $this->createForm(ProfileTypeForm::class, $user,['validation_groups' => ['Default'],]);
            $profileForm->handleRequest($request);

            if (($profileForm->isSubmitted())){
                $em->flush();
                $this->addFlash('success', 'Votre profil a été mis à jour');
            }

        return $this->render('profile/gestionprofile.html.twig',[
            'id' => $id,
            'user' => $user,
            'profileForm' => $profileForm->createView()
        ]);
    }
}
