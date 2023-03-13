<?php

namespace App\Controller;

use App\Entity\Friends;
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
    #[Route('/', name: 'app_public')]
    public function index(UserRepository $UserRepository, EntityManagerInterface $entityManager,PartieRepository $PartieRepository,): Response
    {
// section des profils
        $user = $this->getUser();
        $users = $entityManager->getRepository(User::class)->findAll();
        $leaders = $UserRepository->findBy([], ['victoire' => 'DESC']);



// section des parties
        $parties = $PartieRepository->findAll();
        $partiesDuUser = $entityManager->getRepository(Partie::class)->createQueryBuilder('p')
            ->where('p.statut = :statut')
            ->andWhere('(p.user1 = :user_id OR p.user2 = :user_id)')
            ->setParameters([
                'statut' => 'en cours',
                'user_id' => $this->getUser()->getId(),
            ])
            ->getQuery()
            ->getResult();




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





        return $this->render('public/index.html.twig', [
            'controller_name' => 'PublicController',

// section des profils
            'user' => $user,
            'users' => $users,
            'leaders' => $leaders,

// section des parties
            'parties' => $parties,
            'partiesDuUser' => $partiesDuUser,


// section amis
            'friends' => $friends,
            'friendsR' => $friendsR,
            'partiesAmis' => $partiesAmis

        ]);
    }

    #[Route('/friend/send/{friendId}', name: 'send_friend_request')]
    public function sendFriendRequest(User $friendId, EntityManagerInterface $entityManager): Response
    {

        $friendship = $entityManager->getRepository(Friends::class)->findOneBy([
            'user' => $this->getUser()->getId(),
            'friend' => $friendId,
        ]);

        if (!$friendship) {
            $friend = new Friends();
            $friend->setUser($this->getUser());
            $friend->setFriend($friendId);
            $friend->setStatus('pending');
            $entityManager->persist($friend);
            $entityManager->flush();
            return $this->redirectToRoute('app_public');
        }

        return $this->redirectToRoute('app_public');


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

        return $this->redirectToRoute('app_public');
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

        return $this->redirectToRoute('app_public');
    }

}

