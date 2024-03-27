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
    public function userList(AccesRepository $userAccessLogRepository, UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();
        $userAccessLogs = [];
        foreach ($users as $user) {
            $userAccessLogs[$user->getId()] = $userAccessLogRepository->findBy(['user' => $user]);
        }

        return $this->render('access/index.html.twig', [
            'users' => $users,
            'userAccessLogs' => $userAccessLogs,
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
