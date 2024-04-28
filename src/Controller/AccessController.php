<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Repository\AccesRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/access")
 * 
 * @IsGranted("ROLE_ADMIN", message="Tu ne peut pas acces a cet ressource")
 */
class AccessController extends AbstractController
{

    /**
     * @Route("/users", name="user_acces")
     */
    public function userList(AccesRepository $userAccessLogRepository, UserRepository $userRepository, AccesRepository $acessRepository): Response
    {
        $users = $userRepository->findAll();
        $acces = $acessRepository->findAll();

        // Heure actuelle
        $currentTime = new \DateTime();

        // Déterminer les utilisateurs en ligne et hors ligne
        $onlineUsers = [];
        $offlineUsers = [];


        foreach ($users as $user) {
            // Check if the user has logged out within the last 15 minutes
            $lastLogout = $user->isIsConnect(true) && $user->getStatus(true);

            if ($lastLogout) {
                // User has logged out within the last 15 minutes, considered online
                $onlineUsers[] = $user;
            } else {
                // Otherwise, the user is considered offline
                $offlineUsers[] = $user;
            }
        }



        return $this->render('access/index.html.twig', [
            'onlineUsers' => $onlineUsers,
            'offlineUsers' => $offlineUsers,
            'acces' => $acces
        ]);
    }





    /**
     * @Route("/afficher/{id}", name="user_acces_show", methods={"GET"})
     */
    public function show(User $user): Response
    {


        return $this->render('access/show.html.twig', [
            'user' => $user,

        ]);
    }
}
