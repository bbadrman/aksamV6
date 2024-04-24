<?php


namespace App\EventSubscriber;

use App\Entity\User;
use App\Security\UserDisconnecter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Event\LogoutEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UserLogoutSubscriber implements EventSubscriberInterface
{
    private $entityManager;
    private $security;

    public function __construct(EntityManagerInterface $entityManager, Security $security)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            RequestEvent::class => 'onKernelRequest',
        ];
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();
        $user = $this->security->getUser();

        if (!$user instanceof User) {
            return;
        }

        if (!$user->isIsConnect()) {
            // Déconnectez l'utilisateur si son statut n'est pas enligne  

            // Rediriger l'utilisateur vers la page de connexion
            $response = new RedirectResponse('/deconnexion');
            $event->setResponse($response);
        }
    }
}
