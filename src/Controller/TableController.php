<?php

namespace App\Controller;


use App\Service\StatsService;
use App\Search\SearchProspect;
use App\Form\SearchProspectType;
use App\Repository\ProspectRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RequestStack;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;


/**
 * @Route("/table") 
 * 
 */
class TableController extends AbstractController
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

        private RequestStack $requestStack,
        private ProspectRepository $prospectRepository,
        private Security $security,
        private AuthorizationCheckerInterface $authorizationChecker
    ) {}
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
     * @Route("/traitement", name="app_table_liste", methods={"GET"})
     */
    public function index(StatsService $statsService): Response
    {
        $this->denyAccessUnlessGrantedAuthorizedRoles();

        $stats    = $statsService->getStats();

        return $this->render('prospect/table.html.twig', [
            'stats'    => $stats
        ]);
    }

    /**
     * afficher les prospects injoiniable
     * @Route("/unjoinable", name="app_unjoinable", methods={"GET", "POST"}) 
     */
    public function unjoinable(Request $request): Response

    {
        $this->denyAccessUnlessGrantedAuthorizedRoles();

        $data = new SearchProspect();
        $data->page = $request->query->get('page', 1);
        $form = $this->createForm(SearchProspectType::class, $data);
        $form->handleRequest($this->requestStack->getCurrentRequest());

        $user = $this->security->getUser();
        $roles = $user->getRoles();
        $prospect = [];


        if (in_array('ROLE_SUPER_ADMIN', $roles, true) || in_array('ROLE_ADMIN', $roles, true)) {
            // admi peut voire toutes les nouveaux prospects
            $prospect =  $this->prospectRepository->findUnjoing($data, null);
        } else if (in_array('ROLE_TEAM', $roles, true)) {
            // chef peut voire toutes les nouveaux prospects atacher a leur equipe
            $prospect =  $this->prospectRepository->findUnjoingChef($data,  $user, null);
        } else {
            // cmrcl peut voire seulement les nouveaux prospects atacher a lui
            $prospect =  $this->prospectRepository->findUnjoingCmrcl($data, $user, null);
        }


        return $this->render('prospect/index.html.twig', [
            'prospects' => $prospect,
            'search_form' => $form->createView()
        ]);
    }

    /**
     * afficher les prospect no traiter 
     * @Route("/notrait", name="notrait_index", methods={"GET", "POST"}) 
     */
    public function notrait(Request $request): Response

    {
        $this->denyAccessUnlessGrantedAuthorizedRoles();

        $data = new SearchProspect();
        $data->page = $request->query->get('page', 1);
        $form = $this->createForm(SearchProspectType::class, $data);
        $form->handleRequest($this->requestStack->getCurrentRequest());


        $user = $this->security->getUser();
        $roles = $user->getRoles();

        $prospect = [];


        if (in_array('ROLE_SUPER_ADMIN', $roles, true) || in_array('ROLE_ADMIN', $roles, true)) {
            // admi peut voire toutes les no traite
            $prospect =  $this->prospectRepository->findProspectNonTraiter($data, null);
        } else if (in_array('ROLE_TEAM', $roles, true)) {
            // chef peut voire toutes les no traite atacher a leur equipe
            $prospect =  $this->prospectRepository->findProspectNonTraiterChef($data, $user, null);
        } else {
            // cmrcl peut voire seulement les no traite  atacher a lui
            $prospect =  $this->prospectRepository->findProspectNonTraiterCmrcl($data, $user, null);
        }



        return $this->render('prospect/index.html.twig', [
            'prospects' => $prospect,
            'search_form' => $form->createView()
        ]);
    }

    /**
     * afficher les relance du jour
     * @Route("/relance", name="relancejour_index", methods={"GET", "POST"}) 
     */
    public function relancejour(Request $request,): Response

    {
        $this->denyAccessUnlessGrantedAuthorizedRoles();

        $data = new SearchProspect();
        $data->page = $request->query->get('page', 1);
        $form = $this->createForm(SearchProspectType::class, $data);
        $form->handleRequest($this->requestStack->getCurrentRequest());

        $user = $this->security->getUser();
        $roles = $user->getRoles();

        $prospect = [];


        if (in_array('ROLE_SUPER_ADMIN',  $roles, true) || in_array('ROLE_ADMIN',  $roles, true)) {
            // admi peut voire toutes les relance du jour
            $prospect =  $this->prospectRepository->findRelancedJour($data, null);
        } else if (in_array('ROLE_TEAM',  $roles, true)) {
            // chef peut voire toutes les relance du jour atacher a leur equipe
            $prospect =  $this->prospectRepository->findRelancedJourChef($data, $user, null);
        } else {
            // cmrcl peut voire seulement les relance du jour  atacher a lui
            $prospect =  $this->prospectRepository->findRelancedJourCmrcl($data, $user, null);
        }


        return $this->render('prospect/index.html.twig', [
            'prospects' => $prospect,
            'search_form' => $form->createView()
        ]);
    }


    /**
     * afficher les relance du jour
     * @Route("/relancenotraite", name="relancenotraite_index", methods={"GET", "POST"}) 
     */
    public function relancenotraite(Request $request): Response

    {
        $this->denyAccessUnlessGrantedAuthorizedRoles();

        $data = new SearchProspect();
        $data->page = $request->query->get('page', 1);
        $form = $this->createForm(SearchProspectType::class, $data);
        $form->handleRequest($this->requestStack->getCurrentRequest());
        $user = $this->security->getUser();
        $roles = $user->getRoles();
        $prospect = [];


        if (in_array('ROLE_SUPER_ADMIN', $roles, true) || in_array('ROLE_ADMIN', $roles, true)) {
            // admi peut voire toutes les relance du jour
            $prospect =  $this->prospectRepository->findRelancesNonTraitees($data, null);
            // $numberOfProspects = count($prospect);
            // dd($numberOfProspects);
        } else if (in_array('ROLE_TEAM', $roles, true)) {
            // chef peut voire toutes les relance du jour atacher a leur equipe
            $prospect =   $this->prospectRepository->RelancesNonTraiteesChef($data, $user, null);
        } else {
            // cmrcl peut voire seulement les relance du jour  atacher a lui
            $prospect =   $this->prospectRepository->RelancesNonTraiteesCmrcl($data, $user, null);
        }


        return $this->render('prospect/index.html.twig', [
            'prospects' => $prospect,
            'search_form' => $form->createView()
        ]);
    }
}
