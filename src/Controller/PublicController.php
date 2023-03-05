<?php

namespace App\Controller;

use App\Entity\Partie;
use App\Entity\User;
use App\Repository\PartieRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @method getDoctrine()
 */
class PublicController extends AbstractController
{
    #[Route('/public', name: 'app_public')]
    public function index(UserRepository $UserRepository, EntityManagerInterface $entityManager,PartieRepository $PartieRepository,): Response
    {
        $user = $this->getUser();
        $leaders = $UserRepository->findBy([], ['victoire' => 'DESC']);
        $parties = $PartieRepository->findAll();


        $users = $entityManager->getRepository(User::class)->findAll();
        // Récupérer les entités liées à l'utilisateur
        $partiesDuUser = [];
        $partiesDuUser = $user->getPartie();




        return $this->render('public/index.html.twig', [
            'controller_name' => 'PublicController',
            'user' => $user,
            'parties' => $parties,
            'leaders' => $leaders,
            'partiesDuUser' => $partiesDuUser
        ]);
    }
}

