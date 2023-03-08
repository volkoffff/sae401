<?php


namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Friends;
use Doctrine\ORM\EntityManagerInterface;


class FriendController extends AbstractController
{
    #[Route('/friend/send/{friendId}', name: 'send_friend_request')]
    public function sendFriendRequest(User $friendId, EntityManagerInterface $entityManager): Response
    {

        if ($friendId) {
            $friend = new Friends();
            $friend->setUser($this->getUser());
            $friend->setFriend($friendId);
            $friend->setStatus('pending');

//            dump($friend);
//            die();

            $entityManager->persist($friend);
            $entityManager->flush();
        }

        return $this->redirectToRoute('list_friends');

    }
//    #[Route('/friend/accept/{friendId}', name: 'accept_friend_request')]
//    public function acceptFriendRequest($friendId, EntityManagerInterface $entityManager): Response
//    {
//        $friend = $entityManager->getRepository(Friends::class)->findOneBy([
//            'user_id' => $friendId,
//            'friend_id' => $this->getUser()->getId(),
//            'status' => 'pending'
//        ]);
//
//        if (!$friend) {
//            throw $this->createNotFoundException('Friend request not found');
//        }
//
//        $friend->setStatus('accepted');
//        $entityManager->persist($friend);
//        $entityManager->flush();
//
//        return $this->redirectToRoute('list_friends');
//    }
//
//    #[Route('friend/decline/{friendId}', name: 'decline_friend_request')]
//    public function declineFriendRequest($friendId, EntityManagerInterface $entityManager): Response
//    {
//        $friend = $entityManager->getRepository(Friends::class)->findOneBy([
//            'user_id' => $friendId,
//            'friend_id' => $this->getUser()->getId(),
//            'status' => 'pending'
//        ]);
//
//        if (!$friend) {
//            throw $this->createNotFoundException('Friend request not found');
//        }
//
//        $entityManager->remove($friend);
//        $entityManager->flush();
//
//        return $this->redirectToRoute('list_friends');
//    }

    #[Route('/list', name: 'list_friends')]
    public function listFriends(EntityManagerInterface $entityManager): Response
    {

//        $friends = $entityManager->getRepository(Friends::class)->findBy([
//            'user_id' => $this->getUser()->getId(),
//            'status' => 'accepted'
//        ]);

        $friends = $entityManager->getRepository(Friends::class)->createQueryBuilder('f')
            ->where('f.user = :userId')
            ->andWhere('f.status = :status')
            ->setParameter('userId', $this->getUser()->getId())
            ->setParameter('status', 'accepted')
            ->join('f.user', 'u')
            ->addSelect('u')
            ->getQuery()
            ->getResult();


        $users = $entityManager->getRepository(User::class)->findAll();


        return $this->render('friend/list.html.twig', [
            'friends' => $friends,
            'users' => $users,
            'controller_name' => 'list d ami',
        ]);
    }

}
