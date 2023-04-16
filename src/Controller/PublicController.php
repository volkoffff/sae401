<?php

namespace App\Controller;

use App\Entity\Friends;
use App\Entity\Mot;
use App\Entity\Motpartie;
use App\Entity\Partie;
use App\Entity\User;
use App\Repository\MotPartieRepository;
use App\Repository\MotRepository;
use App\Repository\PartieRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\BiographieType;
use Symfony\Component\String\Slugger\AsciiSlugger;

/**
 * @method getDoctrine()
 */
class PublicController extends AbstractController
{
    #[Route('/', name: 'app_public')]
    public function index(UserRepository $UserRepository, EntityManagerInterface $entityManager, PartieRepository $PartieRepository, Request $request): Response
    {

// section des profils
        $user = $this->getUser();
        $userConnecter = $this->getUser();
        $users = $entityManager->getRepository(User::class)->findAll();
        $leaders = $UserRepository->findBy([], ['trophee' => 'DESC']);



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
        $biographieForm = $this->createForm(BiographieType::class, $user);
        $biographieForm->handleRequest($request);

        if ($biographieForm->isSubmitted() && $biographieForm->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();
        }



        return $this->render('public/index.html.twig', [
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
            return $this->redirectToRoute('app_public');
        }
        $this->addFlash('danger', 'Vous êtes déjà ami avec ce joueur');
        // Rediriger l'utilisateur vers la page d'accueil
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

    #[Route('/create/partie/', name: 'create_partie')]
    public function createPartie(Request $request, MotRepository $motRepository, MotPartieRepository $motpartieRepository): Response
    {
        $slugger = new AsciiSlugger();
        $randomString = bin2hex(random_bytes(2));

        $partie = new Partie();
        $partie->setNom('partie numéro - ' . $randomString);
        $partie->setStatut('en attente');
        $partie->setTour('0');
        $partie->setQuidonne('1');
        $partie->setResultat('en attente');
        $partie->setUser1($this->getUser());
        $partie->setQuijoue('0');
        $partie->setCartej1('15');
        $partie->setCartej2('15');
        $partie->setCartetotal('15');
        $partie->setJeton('9');

        $wordss = $motRepository->findAll();
        $words = [];
        foreach ($wordss as $word) {
            $words[$word->getId()] = $word;
        }
        // Shuffle the words
        $tCartes = [];
        $tCartes[0][1] = 'Noir';
        $tCartes[0][2] = 'Vert';

        $tCartes[1][1] = 'Noir';
        $tCartes[1][2] = 'Noir';

        $tCartes[2][1] = 'Noir';
        $tCartes[2][2] = 'Neutre';

        $tCartes[3][1] = 'Vert';
        $tCartes[3][2] = 'Noir';

        $tCartes[4][1] = 'Neutre';
        $tCartes[4][2] = 'Noir';

        $tCartes[5][1] = 'Vert';
        $tCartes[5][2] = 'Vert';

        $tCartes[6][1] = 'Vert';
        $tCartes[6][2] = 'Vert';

        $tCartes[7][1] = 'Vert';
        $tCartes[7][2] = 'Vert';

        for($i=8; $i<15; $i++) {
            $tCartes[$i][1] = 'Neutre';
            $tCartes[$i][2] = 'Neutre';
        }

        for($i=15; $i<20; $i++) {
            $tCartes[$i][1] = 'Vert';
            $tCartes[$i][2] = 'Neutre';
        }

        for($i=20; $i<25; $i++) {
            $tCartes[$i][1] = 'Neutre';
            $tCartes[$i][2] = 'Vert';
        }
        shuffle($tCartes);
        shuffle($words);


        for($i=0;$i<25;$i++){
            $motpartie = new Motpartie();
            $motpartie->setPartie($partie);
            $motpartie->setMot(array_pop($words));
            $motpartie->setCouleurJ1($tCartes[$i][1]);
            $motpartie->setCouleurJ2($tCartes[$i][2]);
            $motpartie->setEmplacement($i);
            $motpartie->setEtat('libre');
            $motpartieRepository->save($motpartie, true);

        }

        return $this->redirectToRoute('app_profil',[
            'words' => $words,
            'motpartie' => $motpartie,
        ]);

    }


}

