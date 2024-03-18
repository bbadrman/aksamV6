<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Repository\AccesRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


/**
 * @Route("/access")
 */
class AccessController extends AbstractController
{
    /**
     * @Route("/", name="access")
     */
    public function index(UserRepository $userRepository): Response
    {
        // Récupérer tous les utilisateurs
        $users = $userRepository->findAll();

        // Parcourir chaque utilisateur pour obtenir sa date de dernière connexion
        foreach ($users as $user) {
            // Appeler la méthode getLastLoginDate() pour obtenir la date de dernière connexion
            $lastLoginDate = $user->getLastLoginDate();

            // Vous pouvez stocker la date de dernière connexion dans un nouvel attribut de l'utilisateur
            // de sorte qu'il puisse être accessible depuis votre vue Twig
            $user->setLastLoginDate($lastLoginDate);
        }

        // Rendre la vue Twig en passant les données des utilisateurs avec leur date d'accès
        return $this->render('access/index.html.twig', [
            'users' => $users,
        ]);
    }
    /**
     * @Route("/users", name="user_list")
     */
    public function userList(AccesRepository $userAccessLogRepository): Response
    {
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();

        $userAccessLogs = [];
        foreach ($users as $user) {
            $userAccessLogs[$user->getId()] = $userAccessLogRepository->findBy(['user' => $user]);
        }

        return $this->render('access/accesUsers.html.twig', [
            'users' => $users,
            'userAccessLogs' => $userAccessLogs,
        ]);
    }

    /**
     * @Route("/user/list", name="app_user_list")
     */
    public function userListe(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();

        return $this->render('access/accesUserslist.html.twig', [
            'users' => $users,
        ]);
    }
}
