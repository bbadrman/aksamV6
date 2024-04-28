<?php

namespace App\EventSubscriber;

use App\Entity\User;
use App\Entity\Acces;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Event\LogoutEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LogoutSubscriber implements EventSubscriberInterface
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public static function getSubscribedEvents()
    {
        return [
            LogoutEvent::class => 'onLogout',
        ];
    }

    public function onLogout(LogoutEvent $event)
    {
        $user = $event->getToken()->getUser();

        if ($user instanceof User) {
            // $user->setLastLogin(new \DateTime());
            $user->setIsConnect(false);

            try {
                // Enregistrer les modifications de l'utilisateur dans la base de données
                $this->entityManager->persist($user);
                $this->entityManager->flush();

                // Créer une nouvelle instance de l'entité Acces
                $acces = new Acces();
                $acces->setUser($user);
                $acces->setLogoutDate(new \DateTime('+1 hour'));


                // Persistez l'entité dans la base de données
                $this->entityManager->persist($acces);
                $this->entityManager->flush();
            } catch (\Exception $e) {
                // Gérer l'erreur de persistance ici
                // Par exemple, journaliser l'erreur ou afficher un message d'erreur à l'utilisateur
                // Vous pouvez également faire un rollback des transactions si nécessaire
            }
            // if ($user instanceof User) {
            //     $user->setLogoutDate(new \DateTime());
            //     $user->setIsConnect(false);
            //     $user->setLogoutDate(new \DateTime());

            //     $this->entityManager->persist($user);
            //     $this->entityManager->flush();
            // }
        }
    }
}
