<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AcountController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {

        //si l'utilisateur il redirect vers la page dashboard
        if ($this->getUser()) {
            return $this->redirectToRoute('dashboard');
        }





        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user fr dernier nom d'utilisateur saisi par l'utilisateur
        $lastUsername = $authenticationUtils->getLastUsername();


        return $this->render(
            'security/login.html.twig',
            [
                'last_username' => $lastUsername,
                'error' => $error
            ]
        );
    }



    /**
     * @Route("/deconnexion", name="app_logout")
     * @return void
     */
    public function logout(): void
    {
        // Récupérez l'utilisateur actuel
        $user = $this->getUser();

        if ($user instanceof User) {
            $user->setIsConnect(false);
            $this->getDoctrine()->getManager()->flush();
        }

        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
