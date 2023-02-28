<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PublicController extends AbstractController
{
    #[Route('/public', name: 'app_public')]
    public function index(UserRepository $UserRepository): Response
    {
        $user = $this->getUser();
        $leaders = $UserRepository->findBy([], ['victoire' => 'DESC']);
        return $this->render('public/index.html.twig', [
            'controller_name' => 'PublicController',
            'user' => $user,
            'leaders' => $leaders,
        ]);
    }
}

