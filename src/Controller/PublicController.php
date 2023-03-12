<?php

namespace App\Controller;

use App\Entity\Friends;
use App\Entity\partie;
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
        $user = $this->getUser();
        $leaders = $UserRepository->findBy([], ['victoire' => 'DESC']);
        $parties = $PartieRepository->findAll();


        $users = $entityManager->getRepository(User::class)->findAll();
        // Récupérer les entités liées à l'utilisateur
        $partiesDuUser = [];
        $partiesDuUser = $user->getPartie();



        $friendsP = $entityManager->getRepository(Friends::class)->findBy([
            'friend' => $this->getUser()->getId(),
            'status' => 'accepted'
        ]);
        // Récupérer les parties associées aux amis de l'utilisateur actuel

        $friends = $entityManager->getRepository(Friends::class)
            ->createQueryBuilder('f')
            ->where('f.status = :status')
            ->andWhere('f.user = :user OR f.friend = :user')
            ->setParameter('status', 'accepted')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();

        $friendIds = array();
        foreach($friends as $friendship) {
            if($friendship->getUser() == $user) {
                $friendIds[] = $friendship->getFriend();
            } else {
                $friendIds[] = $friendship->getUser();
            }
        }

        $partiesF = $entityManager->getRepository(Partie::class)
            ->createQueryBuilder('p')
            ->innerJoin('p.users', 'u')
            ->andWhere('u.id IN (:friendIds)')
            ->setParameter('friendIds', $friendIds)
            ->getQuery()
            ->getResult();








//parti ami
        $friendsR = $entityManager->getRepository(Friends::class)->findBy([
            'friend' => $this->getUser()->getId(),
            'status' => 'pending'
        ]);
        $friends = $entityManager->getRepository(Friends::class)->createQueryBuilder('f')
            ->where('f.user = :userId')
            ->andWhere('f.status = :status')
            ->setParameter('userId', $this->getUser()->getId())
            ->setParameter('status', 'accepted')
            ->join('f.user', 'u')
            ->addSelect('u')
            ->getQuery()
            ->getResult();
        $friends2 = $entityManager->getRepository(Friends::class)->findBy([
            'friend' => $this->getUser()->getId(),
            'status' => 'accepted'
        ]);
        $users = $entityManager->getRepository(User::class)->findAll();



        return $this->render('public/index.html.twig', [
            'controller_name' => 'PublicController',
            'user' => $user,
            'parties' => $parties,
            'partiesF' => $partiesF,
            'leaders' => $leaders,
            'partiesDuUser' => $partiesDuUser,

// partie amis
            'friends' => $friends,
            'friends2' => $friends2,
            'friendsR' => $friendsR,
            'users' => $users,

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

