<?php

namespace App\Controller;

use App\Entity\Team;
use App\Entity\Prospect;
use App\Service\StatsService;
use App\Search\SearchProspect;
use App\Repository\TeamRepository;
use App\Repository\UserRepository;
use App\Repository\ProspectRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RequestStack;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DashboardController extends AbstractController
{

    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {

        $this->requestStack = $requestStack;
    }

    /**
     * @Route("/", name="dashboard")
     * @IsGranted("ROLE_USER", message="Tu ne peut pas acces a cet ressource")
     
     * @return Response  
     */
    public function index(Request $request,  ProspectRepository $prospectRepository, UserRepository $userRepository,  TeamRepository $teamRepository,  StatsService $statsService,  Security $security, RequestStack $requestStack): Response
    {
        $data = new SearchProspect();
        $user = $security->getUser();
        if (in_array('ROLE_SUPER_ADMIN', $user->getRoles(), true) || in_array('ROLE_ADMIN', $user->getRoles(), true)) {

            // je recupere les prospects qui son pas encors affecter
            $data->page = $request->query->get('page', 1);
            $prospect =  $prospectRepository->findAllSearch($data);
            // $prospectpas = $prospectRepository->findByUserPaAffecter();
            $request->getSession()->set('security', count($prospect));
            // $this->requestStack->getSession()->set('security', count($prospectpas));
        } else if (in_array('ROLE_TEAM', $user->getRoles(), true)) {

            // je recupe seulement les prospects affecter au mon equipe
            $prospect =  $prospectRepository->findOneByChef($user);
            $request->getSession()->set('security', count($prospect));
            // dd($prospect);

        } else {

            $prospect =  $prospectRepository->findByUserConect($data, $user);

            $request->getSession()->set('security', count($prospect));
        }


        $requestStack->getSession()->set('security', count($prospect));

        // generer les données avec statistiques
        $stats    = $statsService->getStats();
        $prosStat =  $prospectRepository->findAll();

        //si tu veux quand tu deconnecter redirect to connexion:
        // $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $users = $userRepository->findAll();
        $team = $teamRepository->findByTeamConect($user);
        $teams = $teamRepository->findAll();
        // $teams = [];
        // dd($teams);  
        return $this->render('dashboard/index.html.twig', [
            'stats'    => $stats,
            'users' => $users,
            'teams' => $team,
            'team' => $teams,
            'prospects' => $prospect,
            'prospstat' => $prosStat
        ]);
    }

    /**
     * Permet d'afficher une seule annonce
     * 
     * @Route("/home/show/{id}", name="dashboard_show")
     *
     * @return Response  
     */

    public function show(Prospect $pro)
    {
        //je récuperer l'annonce qui correspond au slug
        //  $ad = $repo->findOneBySlug($slug);
        return $this->render('dashboard/show.html.twig', [
            'pro' => $pro
        ]);
    }



    /**
     * Permet d'afficher tous les teams
     * 
     *  @Route("/home/list", name="dashboard_list")
     *
     * @return Response  
     */

    public function list(TeamRepository $teamRepository, ProspectRepository $prospectRepository)
    {

        $teams = $teamRepository->findAll();

        return $this->render('dashboard/list.html.twig', [
            'team' => $teams,




        ]);
    }

    /**
     * Permet d'afficher tous les teams
     * 
     *  @Route("/home/show/{id}", name="dashboard_show", methods={"GET"})
     *
     * @return Response  
     */

    public function listShow(Team $team)
    {

        return $this->render('partials/_modal_disp_team.html.twig', [
            'team' => $team,
        ]);
    }
}
