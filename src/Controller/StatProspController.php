<?php

namespace App\Controller;

use App\Form\SearchStatType;
use App\Search\SearchProspect;
use App\Repository\TeamRepository;
use App\Repository\UserRepository;
use App\Repository\ProductRepository;
use App\Repository\ProspectRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RequestStack;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


/**
 * @Route("/stats")
 * @IsGranted("ROLE_ADMIN", message="Tu ne peut pas acces a cet ressource") 
 * 
 */

class StatProspController extends AbstractController
{

    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {

        $this->requestStack = $requestStack;
    }

    #[Route('/{year}/{month}', name: 'prospects_stats', requirements: ['year' => '\d{4}', 'month' => '\d{1,2}'])]
    public function prospectsStats(int $year, int $month, ProspectRepository $prospectRepository): Response
    {
        $prospects = $prospectRepository->findProspectsByMonth($year, $month);
        $currentYear = (int) date('Y');

        return $this->render('stat/stats.html.twig', [
            'prospects' => $prospects,
            'year' => $year,
            'month' => $month,
            'currentYear' => $currentYear,
        ]);
    }

    #[Route('/team', name: 'prospects_statst_team')]
    public function prospectsStatsTeam(TeamRepository $teamRepository, ProspectRepository $prospectRepository): Response
    {

        $data = new SearchProspect();
        $form = $this->createForm(SearchStatType::class, $data);
        $form->handleRequest($this->requestStack->getCurrentRequest());

        $prospectsCount = $prospectRepository->findSearchStat($data);
        $prospectsByTeam = [];
        if ($prospectsCount > 0) {
            $prospects = $prospectRepository->findSearch($data);
            foreach ($prospects as $prospect) {
                $team = $prospect->getTeam();
                if ($team !== null) {
                    $teamName = $team->getName();
                    $prospectsByTeam[$teamName][] = $prospect;
                }
            }
        } else {
            $prospects = null;
        }

        $teams = $teamRepository->findAll();

        return $this->render('stat/tableteam.html.twig', [
            'prospectsByTeam' => $prospectsByTeam,
            'prospectsCount' => $prospectsCount,
            'prospects' => $prospects,
            'team' => $teams,
            'search_form' => $form->createView()
        ]);
    }

    #[Route('/teams', name: 'prospects_statstm_team')]
    public function prospectsStatsTeamM(TeamRepository $teamRepository, ProspectRepository $prospectRepository): Response
    {
        $data = new SearchProspect();
        $form = $this->createForm(SearchStatType::class, $data);
        $form->handleRequest($this->requestStack->getCurrentRequest());

        // Utiliser la logique de la deuxième fonction pour obtenir les prospects par équipe et mois
        $currentDate = new \DateTime();
        $year = $data->getYear() ?? date('Y');
        $month = $data->getMonth() ?? $currentDate->format('n');

        $teams = $teamRepository->findAll();
        $prospectsByTeam = [];

        foreach ($teams as $team) {
            $prospectsByTeam[$team->getName()] = $prospectRepository->findByMonthAndTeam($year, $month, $team->getId());
        }

        // Optionnel : obtenir le nombre total de prospects pour affichage
        $totalProspectsCount = $prospectRepository->findProspectsByMonth($year, $month);

        return $this->render('stat/tableteamt.html.twig', [
            'prospectsByTeam' => $prospectsByTeam,
            'prospects' => $totalProspectsCount,
            'team' => $teams,
            'search_form' => $form->createView()
        ]);
    }


    #[Route('/team/{year}/{month}', name: 'prospects_stats_team', requirements: ['year' => '\d{4}', 'month' => '\d{1,2}'])]
    public function prospectsStatsTeamt(int $year, int $month, TeamRepository $teamRepository, ProductRepository $productRepository, ProspectRepository $prospectRepository, int $comrclId = null): Response
    {

        $teams = $teamRepository->findAll();
        $prospectsByTeam = [];
        $prospectsByCmrcl = [];

        // Statistiques par équipe
        foreach ($teams as $team) {
            $prospectsByTeam[$team->getName()] = $prospectRepository->findByMonthAndTeam($year, $month, $team->getId(), $comrclId);
        }

        // Statistiques par commercial pour chaque équipe
        foreach ($teams as $team) {
            $users = $team->getUsers();
            foreach ($users as $user) {
                $prospectsByCmrcl[$team->getName()][$user->getUserIdentifier()] = $prospectRepository->findByMonthTeamAndComrcl($year, $month, $team->getId(), $user->getId());
            }
        }
        $product = $productRepository->findProductByMonth($year, $month);
        $prospects = $prospectRepository->findProspectsByMonth($year, $month);
        $currentYear = (int) date('Y');

        return $this->render('stat/tableteam.html.twig', [
            'prospectsByTeam' => $prospectsByTeam,
            'prospects' => $prospects,
            'team' =>  $teams,
            'year' => $year,
            'month' => $month,
            'currentYear' => $currentYear,
            'prospectsByCmrcl' => $prospectsByCmrcl,
            'products' => $product,


        ]);
    }

    #[Route('/cmrcl/{year}/{month}', name: 'prospects_stats_cmrcl', requirements: ['year' => '\d{4}', 'month' => '\d{1,2}'])]
    public function prospectsStatsCmrcl(int $year, int $month, UserRepository $userRepository,  TeamRepository $teamRepository, ProspectRepository $prospectRepository): Response
    {
        $comrcls = $userRepository->findAll();
        $teams = $teamRepository->findAll();
        $prospectsByTeam = [];

        // foreach ($comrcls as $comrcl) {
        //     $prospectsByTeam[$comrcl->getUserIdentifier()] = $prospectRepository->findByMonthAndComrcl($year, $month, $comrcl->getId());
        // }
        foreach ($teams as $team) {
            $prospectsByTeam[$team->getName()] = [];
            foreach ($team->getUsers() as $user) {
                $prospectsByTeam[$team->getName()][$user->getUserIdentifier()] = $prospectRepository->findByMonthTeamAndComrcl($year, $month, $team->getId(), $user->getId());
            }
        }
        $prospects = $prospectRepository->findProspectsByMonth($year, $month);

        $currentYear = (int) date('Y');

        return $this->render('stat/statsCmrcl.html.twig', [
            'prospectsByTeam' => $prospectsByTeam,
            'prospects' => $prospects,
            // 'team' =>  $teams,
            'year' => $year,
            'month' => $month,
            'currentYear' => $currentYear,
        ]);
    }


    #[Route('/statstype/{year}/{month}', name: 'prospects_statype', requirements: ['year' => '\d{4}', 'month' => '\d{1,2}'])]
    public function prospectsStatsType(int $year, int $month, ProspectRepository $prospectRepository, ProductRepository $productRepository): Response
    {
        $product = $productRepository->findProductByMonth($year, $month);
        $products = $productRepository->findAll();

        $prospects = $prospectRepository->findProspectsByMonth($year, $month);
        $currentYear = (int) date('Y');

        return $this->render('stat/statype.html.twig', [
            'products' => $product,
            'product' => $products,
            // dd($product),
            'prospects' => $prospects,

            'year' => $year,
            'month' => $month,
            'currentYear' => $currentYear,
        ]);
    }
    #[Route('/product/{year}/{month}', name: 'prospects_statypet', requirements: ['year' => '\d{4}', 'month' => '\d{1,2}'])]
    public function prospectsStatsTypeTest(int $year, int $month, ProspectRepository $prospectRepository, ProductRepository $productRepository): Response
    {
        $products = $productRepository->findAll();

        // Fetch all prospects with team and product information for the month
        $prospects = $prospectRepository->findByMonthWithTeamAndProduct($year, $month);

        // Group prospects by team
        $prospectsByTeam = [];
        foreach ($prospects as $prospect) {
            $teamName = $prospect->getTeam()->getName();
            $product = $prospect->getProduct();  // Get the product reference first

            if ($product) { // Check if product exists before accessing its ID
                $productId = $product->getId();
                $prospectsByTeam[$teamName][$productId] = isset($prospectsByTeam[$teamName][$productId]) ? $prospectsByTeam[$teamName][$productId] + 1 : 1;
            } else {
                // Handle cases where there's no associated product (optional)
                // You can log a message, set a default value for `$productId`, etc.
            }
        }


        $currentYear = (int) date('Y');

        return $this->render('stat/statProduct.html.twig', [
            'products' => $products,
            'prospectsByTeam' => $prospectsByTeam, // Nested structure with team & product counts
            'year' => $year,
            'month' => $month,
            'currentYear' => $currentYear,
        ]);
    }
}
