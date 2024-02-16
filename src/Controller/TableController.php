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


/**
 * @Route("/table")
 * @IsGranted("ROLE_USER", message="Tu ne peut pas acces a cet ressource")
 * 
 */
class TableController extends AbstractController
{

    private $requestStack;
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * @Route("/", name="app_table_liste", methods={"GET"})
     */
    public function index(StatsService $statsService): Response
    {
        $stats    = $statsService->getStats();

        return $this->render('prospect/table.html.twig', [
            'stats'    => $stats
        ]);
    }

    /**
     * afficher les prospects injoiniable
     * @Route("/unjoinable", name="app_unjoinable", methods={"GET", "POST"}) 
     */
    public function unjoinable(Request $request,  ProspectRepository $prospectRepository, Security $security, StatsService $statsService): Response

    {
        $data = new SearchProspect();
        $data->page = $request->query->get('page', 1);
        $form = $this->createForm(SearchProspectType::class, $data);
        $form->handleRequest($this->requestStack->getCurrentRequest());
        $user = $security->getUser();
        $prospect = [];


        if (in_array('ROLE_SUPER_ADMIN', $user->getRoles(), true) || in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            // admi peut voire toutes les nouveaux prospects
            $prospect =  $prospectRepository->findUnjoing($data, null);
        } else if (in_array('ROLE_TEAM', $user->getRoles(), true)) {
            // chef peut voire toutes les nouveaux prospects atacher a leur equipe
            $prospect =  $prospectRepository->findUnjoingChef($data,  $user, null);
        } else {
            // cmrcl peut voire seulement les nouveaux prospects atacher a lui
            $prospect =  $prospectRepository->findUnjoingCmrcl($data, $user, null);
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
    public function notrait(Request $request,  ProspectRepository $prospectRepository,  Security $security): Response

    {
        $data = new SearchProspect();
        $data->page = $request->query->get('page', 1);
        $form = $this->createForm(SearchProspectType::class, $data);
        $form->handleRequest($this->requestStack->getCurrentRequest());

        $user = $security->getUser();

        $prospect = [];


        if (in_array('ROLE_SUPER_ADMIN', $user->getRoles(), true) || in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            // admi peut voire toutes les no traite
            $prospect =  $prospectRepository->findNonTraiter($data, null);
        } else if (in_array('ROLE_TEAM', $user->getRoles(), true)) {
            // chef peut voire toutes les no traite atacher a leur equipe
            $prospect =  $prospectRepository->findNonTraiterChef($data, $user, null);
        } else {
            // cmrcl peut voire seulement les no traite  atacher a lui
            $prospect =  $prospectRepository->findNonTraiterCmrcl($data, $user, null);
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
    public function relancejour(Request $request,  ProspectRepository $prospectRepository,  Security $security): Response

    {
        $data = new SearchProspect();
        $data->page = $request->query->get('page', 1);
        $form = $this->createForm(SearchProspectType::class, $data);
        $form->handleRequest($this->requestStack->getCurrentRequest());
        $user = $security->getUser();

        $prospect = [];


        if (in_array('ROLE_SUPER_ADMIN', $user->getRoles(), true) || in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            // admi peut voire toutes les relance du jour
            $prospect =  $prospectRepository->findRelanced($data, null);
        } else if (in_array('ROLE_TEAM', $user->getRoles(), true)) {
            // chef peut voire toutes les relance du jour atacher a leur equipe
            $prospect =  $prospectRepository->findRelancedChef($data, $user, null);
        } else {
            // cmrcl peut voire seulement les relance du jour  atacher a lui
            $prospect =  $prospectRepository->findRelancedCmrcl($data, $user, null);
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
    public function relancenotraite(Request $request,  ProspectRepository $prospectRepository,  Security $security): Response

    {
        $data = new SearchProspect();
        $data->page = $request->query->get('page', 1);
        $form = $this->createForm(SearchProspectType::class, $data);
        $form->handleRequest($this->requestStack->getCurrentRequest());
        $user = $security->getUser();

        $prospect = [];


        if (in_array('ROLE_SUPER_ADMIN', $user->getRoles(), true) || in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            // admi peut voire toutes les relance du jour
            $prospect =  $prospectRepository->findRelancesNonTraitees($data, null);
            // $numberOfProspects = count($prospect);
            // dd($numberOfProspects);
        } else if (in_array('ROLE_TEAM', $user->getRoles(), true)) {
            // chef peut voire toutes les relance du jour atacher a leur equipe
            $prospect =  $prospectRepository->RelancesNonTraiteesChef($data, $user, null);
        } else {
            // cmrcl peut voire seulement les relance du jour  atacher a lui
            $prospect =  $prospectRepository->RelancesNonTraiteesCmrcl($data, $user, null);
        }


        return $this->render('prospect/index.html.twig', [
            'prospects' => $prospect,
            'search_form' => $form->createView()
        ]);
    }
}
