<?php

namespace App\Controller;

use App\Entity\Friends;
use App\Entity\Partie;
use App\Entity\User;
use App\Form\BiographieType;
use App\Form\ProfilModificationType;
use App\Repository\PartieRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfilModificationController extends AbstractController
{
    #[Route('/profil/modification', name: 'app_profil_modification')]
    public function index(UserRepository $UserRepository, EntityManagerInterface $entityManager, PartieRepository $PartieRepository, Request $request): Response
    {


// section des profils
        $user = $this->getUser();
        $userConnecter = $this->getUser();
        $users = $entityManager->getRepository(User::class)->findAll();
        $leaders = $UserRepository->findBy([], ['victoire' => 'DESC']);

        $form = $this->createForm(ProfilModificationType::class, $user);


// section des parties
        $parties = $PartieRepository->findAll();
        $partiesDuUser = 0;
        if (!empty($parties)) {
            $partiesDuUser = $entityManager->getRepository(Partie::class)->createQueryBuilder('p')
                ->Where('(p.user1 = :user_id OR p.user2 = :user_id)')
                ->setParameters([
                    'user_id' => $this->getUser()->getId(),
                ])
                ->getQuery()
                ->getResult();
        }




//section ami

        $friendsR = $entityManager->getRepository(Friends::class)->findBy([
            'friend' => $this->getUser()->getId(),
            'status' => 'pending'
        ]);
        $friends = 0;
        $friends = $entityManager->getRepository(Friends::class)->createQueryBuilder('f')
            ->where('f.user = :userId OR f.friend = :userId')
            ->andWhere('f.status = :status ')
            ->setParameter('userId', $this->getUser()->getId())
            ->setParameter('status', 'accepted')
            ->getQuery()
            ->getResult();

        // section des parties
        $partiesAmis = 0;
        if (!empty($friends)) {
            foreach ($friends as $friendship) {
                $friendsIds[] = $friendship->getUser() === $user ? $friendship->getFriend() : $friendship->getUser();
            }
            $partiesAmis = $PartieRepository->createQueryBuilder('p')
                ->where('p.statut = :statut')
                ->andWhere('p.user1 IN (:friendsIds) OR p.user2 IN (:friendsIds)')
                ->setParameters([
                    'statut' => 'en attente',
                    'friendsIds' => $friendsIds,
                ])
                ->getQuery()
                ->getResult();
        }
        // section des parties
        $partiesSansAmis = 0;
        if (!empty($friends)) {
            foreach ($friends as $friendship) {
                $friendsIds[] = $friendship->getUser() === $user ? $friendship->getFriend() : $friendship->getUser();
            }
            $partiesSansAmis = $PartieRepository->createQueryBuilder('p')
                ->where('p.statut = :statut')
                ->andWhere('p.user1 NOT IN (:friendsIds) OR p.user2 NOT IN (:friendsIds)')
                ->setParameters([
                    'statut' => 'en attente',
                    'friendsIds' => $friendsIds,
                ])
                ->getQuery()
                ->getResult();
        }

        $biographieForm = $this->createForm(BiographieType::class, $user);
        $biographieForm->handleRequest($request);

        if ($biographieForm->isSubmitted() && $biographieForm->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();
        }



        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Enregistre les modifications dans la base de données
//            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            // Redirige l'utilisateur vers la page de profil
            return $this->redirectToRoute('app_profil');
        }

        return $this->render('profil/modification.html.twig', [
            'form' => $form->createView(),
            'controller_name' => 'PublicController',
            'biographie_form' => $biographieForm->createView(),
// section des profils
            'user' => $user,
            'userConnecter' => $userConnecter,
            'users' => $users,
            'leaders' => $leaders,

// section des parties
            'parties' => $parties,
            'partiesDuUser' => $partiesDuUser,
            'partiesSansAmis' => $partiesSansAmis,


// section amis
            'friends' => $friends,
            'friendsR' => $friendsR,
            'partiesAmis' => $partiesAmis

        ]);
    }
//    #[Route('/profil/modification', name: 'app_profil_modification_action', methods: ['POST'])]
//    public function changeProfil(UserRepository $UserRepository, EntityManagerInterface $entityManager, PartieRepository $PartieRepository, Request $request): Response
//    {
//
//        $user = $this->getUser();
//        $email = $request->request->get('email');
//        if ($email !== null && $email !== '') {
//            $user->setEmail($email);
//        }
//
//        $pseudo = $request->request->get('pseudo');
//        if ($pseudo !== null && $pseudo !== '') {
//            $user->setPseudo($pseudo);
//        }
//
//        return $this->redirectToRoute('app_profil');
//    }

    #[Route('/friend/send/{friendId}', name: 'send_friend_request')]
    public function sendFriendRequest(User $friendId, EntityManagerInterface $entityManager): Response
    {

        $friendship = $entityManager->getRepository(Friends::class)->findOneBy([
            'user' => $this->getUser()->getId(),
            'friend' => $friendId,
        ]);
        $friendship2 = $entityManager->getRepository(Friends::class)->findOneBy([
            'user' => $friendId,
            'friend' =>  $this->getUser()->getId(),
        ]);

        if (!$friendship and !$friendship2) {
            $friend = new Friends();
            $friend->setUser($this->getUser());
            $friend->setFriend($friendId);
            $friend->setStatus('pending');
            $entityManager->persist($friend);
            $entityManager->flush();
            return $this->redirectToRoute('app_join_partie');
        }
        $this->addFlash('danger', 'Vous êtes déjà ami avec ce joueur');
        // Rediriger l'utilisateur vers la page d'accueil
        return $this->redirectToRoute('app_join_partie');


    }
    #[Route('/friend/accept/{friendId}', name: 'accept_friend_request')]
    public function acceptFriendRequest($friendId, EntityManagerInterface $entityManager): Response
    {
        $friend = $entityManager->getRepository(Friends::class)->findOneBy([
            'user' => $friendId,
            'friend' => $this->getUser()->getId(),
            'status' => 'pending'
        ]);


        if (!$friend) {
            throw $this->createNotFoundException('Friend request not found');
        }

        $friend->setStatus('accepted');
        $entityManager->persist($friend);
        $entityManager->flush();

        return $this->redirectToRoute('app_join_partie');
    }

    #[Route('friend/decline/{friendId}', name: 'decline_friend_request')]
    public function declineFriendRequest($friendId, EntityManagerInterface $entityManager): Response
    {
        $friend = $entityManager->getRepository(Friends::class)->findOneBy([
            'user' => $friendId,
            'friend' => $this->getUser()->getId(),
            'status' => 'pending'
        ]);

        if (!$friend) {
            throw $this->createNotFoundException('Friend request not found');
        }

        $entityManager->remove($friend);
        $entityManager->flush();

        return $this->redirectToRoute('app_join_partie');
    }



    #[Route('/partie/join/{partieId}', name: 'join_partie')]
    public function joinPartie( $partieId, EntityManagerInterface $entityManager): Response
    {

        $partie = $entityManager->getRepository(Partie::class)->findOneBy([
            'id' => $partieId
        ]);

        if (!$partie) {
            throw $this->createNotFoundException('aucune partie a rejoindre');
        };
        if ($partie->getUser1() === $this->getUser()) {
            $this->addFlash('danger', 'Vous êtes le créateur de cette partie');
            // Rediriger l'utilisateur vers la page d'accueil
            return $this->redirectToRoute('app_join_partie');
        }

        $partie->setUser2($this->getUser());
        $partie->setStatut('en cours');
        $entityManager->persist($partie);
        $entityManager->flush();

        return $this->redirectToRoute('app_public');
    }


}
