<?php

namespace App\EventSubscriber;

use App\Entity\Acces;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Doctrine\ORM\EntityManagerInterface;

class AuthenticationSuccessSubscriber implements EventSubscriberInterface
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public static function getSubscribedEvents()
    {
        return [
            InteractiveLoginEvent::class => 'onAuthenticationSuccess',
        ];
    }

    public function onAuthenticationSuccess(InteractiveLoginEvent $event)
    {
        $user = $event->getAuthenticationToken()->getUser();

        if ($user) {
            // Créez une nouvelle instance de l'entité Acces
            $acces = new Acces();
            $acces->setUser($user);
            $acces->setAccessDate(new \DateTime());

            // Persistez l'entité dans la base de données
            $this->entityManager->persist($acces);
            $this->entityManager->flush();
        }
    }
}
