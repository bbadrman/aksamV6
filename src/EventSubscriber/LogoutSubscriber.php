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
        // $user = $event->getToken()->getUser();

        // if ($user instanceof User) {
        //     $user->setLogoutDate(new \DateTime());
        //     $user->setIsConnect(false);
        //     $user->setLogoutDate(new \DateTime());

        //     $this->entityManager->persist($user);
        //     $this->entityManager->flush();
        // }
    }
}
