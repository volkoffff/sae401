<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;



class SecurityController extends AbstractController
{
    #[Route('/inscription', name: 'security_registration')]
    public function registration(Request $request, ObjectManager $manager, UserPasswordHasherInterface $passwordHasher) {
        $user = new User();

        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()) {
                $hashedPassword = $passwordHasher->hashPassword($user, $user->getPassword());
                $user->setPassword($hashedPassword);
                $avatar = $form->get('avatar')->getData();
                $user->setAvatar($avatar);


                $manager->persist($user);
                $manager->flush();
                return $this->redirectToRoute('security_login');
            }


        return $this->render('security/registration.html.twig', [
           'form' => $form->createView(),
            'controller_name' => 'SecurityController',

        ]);
    }

    #[Route('/connexion', name: 'security_login')]
    public function Login() {


        return $this->render('security/login.html.twig', [
            'controller_name' => 'SecurityController',
        ]);
    }

    #[Route('/deconnexion', name: 'security_logout')]
    public function Logout() {
    }
}
