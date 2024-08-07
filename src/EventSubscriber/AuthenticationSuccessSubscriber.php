<?php

namespace App\EventSubscriber;

use App\Entity\User;
use App\Entity\Acces;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class AuthenticationSuccessSubscriber implements EventSubscriberInterface
{

    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            InteractiveLoginEvent::class => 'onAuthenticationSuccess',
        ];
    }
    /**
     * pour gestion de la session
     */
    public function onAuthenticationSuccess(InteractiveLoginEvent $event)
    {
        $user = $event->getAuthenticationToken()->getUser();

        if ($user instanceof User) {
            // if ($user->isIsConnect(true)) {
            //     // Déconnectez l'utilisateur et jetez une exception
            //     throw new AuthenticationException('This user is already logged in elsewhere.');
            // }
            // $user->setLastLogin(new \DateTime());
            $user->setIsConnect(true);

            try {
                // Enregistrer les modifications de l'utilisateur dans la base de données
                $this->entityManager->persist($user);
                $this->entityManager->flush();

                // Créer une nouvelle instance de l'entité Acces
                $acces = new Acces();
                $acces->setUser($user);
                $acces->setAccessDate(new \DateTime('+1 hour'));


                // Persistez l'entité dans la base de données
                $this->entityManager->persist($acces);
                $this->entityManager->flush();
            } catch (\Exception $e) {
                // Gérer l'erreur de persistance ici
                // Par exemple, journaliser l'erreur ou afficher un message d'erreur à l'utilisateur
                // Vous pouvez également faire un rollback des transactions si nécessaire
            }
        }
    }
}
