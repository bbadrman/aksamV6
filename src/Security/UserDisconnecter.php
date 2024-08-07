<?php

namespace App\Security;

use App\Entity\User;
use App\Entity\Acces;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

class UserDisconnecter
{


    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    /**  
     * @return void
     */
    public function disconnectUser(UserInterface $user, Request $request): void
    {

        /** @var User $user */

        // Mettre à jour l'état de connexion et l'activation de l'utilisateur
        $user->setIsConnect(false);
        // $user->setLogoutDate(new \DateTime());
        // $user->setStatus(false);
        // Enregistrer les modifications dans la base de données
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        // Créer une nouvelle instance d'Acces
        $acces = new Acces();
        $acces->setUser($user); // Associer l'utilisateur à l'accès

        if (!$acces->getLogoutDate()) {
            $acces->setLogoutDate(new \DateTime('+1 hour'));
        }
        // $acces->setLogoutDate(new \DateTime('+1 hour'));

        // Persistez l'entité dans la base de données
        $this->entityManager->persist($acces);
        $this->entityManager->flush();
    }
}
