<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfileType;
use App\Repository\UserRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\DebugUnitOfWorkListener;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
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
    #[Route('/update', name: 'profil_update', methods: ['GET', 'POST'])]
        public function update(Request $request,UserRepository $userRepository,
                               UserPasswordHasherInterface $userPasswordHasher,
                               EntityManagerInterface $em, FileUploader $fileUploader): Response{
        /*Je cherche dans la class $userRepository qui permet d'accéder à tous mes utilisateurs l'utilisateur avec :
                                     "findOneBy(['id'=>$this->getUser()->getId()])"
                    je cherche l'utilisateur ayant le même id que le "user" en cour d'utilisation du site */

            $user = $userRepository->findOneBy(['id'=>$this->getUser()->getId()]);
            $profileForm = $this->createForm(ProfileType::class, $user,['validation_groups' => ['Default'],]);
            $profileForm->handleRequest($request);

            /*Je teste si l'utilisateur est en train de soumettre un formulaire et si ce formulaire est valide
                    je récupère le mot de passe du champ 1 'password' dans '$password'
                     et celui du champ 2 'confirmation' dans '$confirmation'*/

            /*TODO : Gérer les assets de vérification formulaire users.*/
            if (($profileForm->isSubmitted() && $profileForm->isValid())){
                $confirmation=$profileForm->get('passwordConfirmation')->getData();
                $password=$profileForm->get('password')->getData();

                //uploader nos image
                /** @var  UploadedFile $photoProfil */
                $photoProfil = $profileForm->get('photo')->getData();
                if ($photoProfil){
                    $backdrop = $fileUploader->upload($photoProfil, '/avatar');
                    $user->setPhoto($backdrop);
                }

            /*Je test que mon l'email renseigné dans mon champ confirmation est identique au mot de passe
                    Si oui je mofifie le mot de passe dans la BDD en le hachant
                    Si non j'affiche un message d'errreur
                    Si aucune modif de mot de passe n'est souhaitée je valide directemnt les changements sans
                    m'occuper du mot de passe, & j'affiche un message de "success*/

                if (isset($confirmation) && isset($password)){
                    if ($confirmation ===  $profileForm->get('password')->getData() ){
                        $user->setPassword(
                            $userPasswordHasher->hashPassword(
                                $user,
                                $profileForm->get('password')->getData()
                            )
                        );
                        $em->flush();
                        $this->addFlash('success', 'Votre profil a été mis à jour');
                        // Rediriger l'internaute vers l'accueil
                        return $this->redirectToRoute('sorties');
                    }else{
                        $this->addFlash('error', 'Les mots de passe ne sont pas identiques');
                    }
                }else{
                    $em->flush();
                    $this->addFlash('success', 'Votre profil a été mis à jour');
                    // Rediriger l'internaute vers l'accueil
                    return $this->redirectToRoute('sorties');
                }

            }

        return $this->render('profile/gestionprofile.html.twig',[
            'user' => $user,
            'profileForm' => $profileForm->createView()
        ]);
    }
}
