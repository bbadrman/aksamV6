<?php

namespace App\Security;

use App\Entity\User;
use App\Entity\Acces;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;

class LoginFormAuthAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';



    public function __construct(private UrlGeneratorInterface $urlGenerator, private EntityManagerInterface  $entityManager, private UserRepository $userRepository, private TokenStorageInterface $tokenStorage)
    {
    }

    public function authenticate(Request $request): Passport
    {
        $username = $request->request->get('username', '');

        $request->getSession()->set(Security::LAST_USERNAME, $username);

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['username' => $username]);

        if (!$user) {
            // Échec de l'authentification avec une erreur personnalisée
            throw new CustomUserMessageAuthenticationException('Ce nom d\'utilisateur est introuvable.');
        }

        // $token = $this->tokenStorage->getToken();

        // if ($token !== null && $token->getUser() instanceof User && $token->getUser()->getUsername() === $username) {
        //     // The user is already connected, throw an exception
        //     throw new CustomUserMessageAuthenticationException('Cet utilisateur est déjà connecté.');
        // }



        return new Passport(
            new UserBadge($username),
            new PasswordCredentials($request->request->get('password', '')),
            [
                new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
            ]
        );
    }


    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        /** @var User $user */
        $user = $token->getUser();
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        // // Créez une nouvelle instance de l'entité Acces
        // $acces = new Acces();
        // // Définissez la date d'accès sur la date actuelle
        // $acces->setAccessDate(new \DateTime());
        // // Associez l'utilisateur à l'entité Acces
        // $acces->setUser($user);

        // // Persistez l'entité Acces dans la base de données
        // $this->entityManager->persist($acces);
        // $this->entityManager->flush();



        /** @var User $user */
        $user = $token->getUser();

        if (in_array('ROLE_SUPER_ADMIN', $user->getRoles(), true) || in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            return new RedirectResponse($this->urlGenerator->generate('dashboard'));
        }

        if (in_array('ROLE_PROS', $user->getRoles(), true)) {
            return new RedirectResponse($this->urlGenerator->generate('app_table_liste'));
        }
        if (in_array('ROLE_ADD_PROS', $user->getRoles(), true)) {
            return new RedirectResponse($this->urlGenerator->generate('app_prospect_new'));
        }
        if (in_array('ROLE_EDIT_PROS', $user->getRoles(), true)) {
            return new RedirectResponse($this->urlGenerator->generate('app_table_liste'));
        }


        if (in_array('ROLE_PROD', $user->getRoles(), true)) {
            return new RedirectResponse($this->urlGenerator->generate('app_product_index'));
        }
        if (in_array('ROLE_ADD_PROD', $user->getRoles(), true)) {
            return new RedirectResponse($this->urlGenerator->generate('app_prospect_new'));
        }
        if (in_array('ROLE_EDIT_PROD', $user->getRoles(), true)) {
            return new RedirectResponse($this->urlGenerator->generate('app_product_index'));
        }



        if (in_array('ROLE_USER', $user->getRoles(), true)) {
            return new RedirectResponse($this->urlGenerator->generate('dashboard'));
        }


        if (in_array('ROLE_RH', $user->getRoles(), true)) {
            return new RedirectResponse($this->urlGenerator->generate('app_team_index'));
        }

        if (in_array('ROLE_ADD_RH', $user->getRoles(), true)) {
            return new RedirectResponse($this->urlGenerator->generate('team_new'));
        }

        return new RedirectResponse($this->urlGenerator->generate('dashboard'));
    }


    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
