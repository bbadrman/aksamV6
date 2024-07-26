<?php

namespace App\Controller;


use App\Search\SearchProspect;
use App\Form\SearchProspectType;
use App\Repository\ProspectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;


class SearchController extends AbstractController
{
    private const AUTHORIZED_ROLES = [
        'ROLE_ADMIN',
        'ROLE_TEAM',
        'ROLE_AFFECT',
        'ROLE_ADD_PROS',
        'ROLE_EDIT_PROS',
        'ROLE_PROS',
        'ROLE_COMERC'
    ];



    public function __construct(
        private EntityManagerInterface $entityManager,
        private ProspectRepository $prospectRepository,
        private AuthorizationCheckerInterface $authorizationChecker,
        private Security $security
    ) {
    }
    private function denyAccessUnlessGrantedAuthorizedRoles(): void
    {
        if (!$this->getUser()) {
            throw new AccessDeniedException("Accès refusé pour les utilisateurs anonymes");
        }

        foreach (self::AUTHORIZED_ROLES as $role) {
            if ($this->authorizationChecker->isGranted($role)) {
                return;
            }
        }

        throw new AccessDeniedException("Tu ne peux pas accéder à cette ressource");
    }

    /**
     * Search for all prospects
     * @Route("/search_prospect", name="prospect_search", methods={"GET"}) 
     */
    public function search(Request $request): Response
    {
        $this->denyAccessUnlessGrantedAuthorizedRoles();

        $data = new SearchProspect();
        $data->page = $request->get('page', 1);

        $form = $this->createForm(SearchProspectType::class, $data);
        $form->handleRequest($request);

        $user = $this->security->getUser();
        $roles = $user->getRoles();
        $prospect = [];




        if ($form->isSubmitted() && $form->isValid() && !$form->isEmpty()) {

            if (in_array('ROLE_SUPER_ADMIN',  $roles, true) || in_array('ROLE_ADMIN',  $roles, true) || in_array('ROLE_AFFECT',  $roles, true)) {
                // admi peut chercher toutes les prospects
                $prospect = $this->prospectRepository->findSearch($data, $user);
            } else if (in_array('ROLE_TEAM',  $roles, true)) {
                // chef peut chercher toutes les prospects atacher a leur equipe
                $prospect = $this->prospectRepository->findAllChefSearch($data, $user);
            } elseif (in_array('ROLE_USER',  $roles, true)) {
                // cmrcl peut chercher seulement les prospects atacher a lui
                $prospect = $this->prospectRepository->findByUserAffecterCmrcl($data, $user);
            }

            return $this->render('prospect/index.html.twig', [
                'prospects' => $prospect,
                'search_form' => $form->createView()
            ]);
        }



        return $this->render('prospect/search.html.twig', [
            'prospects' => $prospect,
            'search_form' => $form->createView(),
        ]);
    }
}
