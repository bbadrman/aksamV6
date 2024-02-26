<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Repository\TeamRepository;
use App\Repository\ProspectRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StatProspController extends AbstractController
{
    #[Route('/prospects/stats/{year}/{month}', name: 'prospects_stats', requirements: ['year' => '\d{4}', 'month' => '\d{1,2}'])]
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
    #[Route('/prospects/stats/team/{year}/{month}', name: 'prospects_stats_team', requirements: ['year' => '\d{4}', 'month' => '\d{1,2}'])]
    public function prospectsStatsTeam(int $year, int $month, TeamRepository $teamRepository, ProspectRepository $prospectRepository): Response
    {
        $teams = $teamRepository->findAll();
        $prospectsByTeam = [];

        foreach ($teams as $team) {
            $prospectsByTeam[$team->getName()] = $prospectRepository->findByMonthAndTeam($year, $month, $team->getId());
        }

        $currentYear = (int) date('Y');

        return $this->render('stat/tablejs.html.twig', [
            'prospectsByTeam' => $prospectsByTeam,
            'year' => $year,
            'month' => $month,
            'currentYear' => $currentYear,
        ]);
    }

    #[Route('/prospects/stats/cmrcl/{year}/{month}', name: 'prospects_stats_cmrcl', requirements: ['year' => '\d{4}', 'month' => '\d{1,2}'])]
    public function prospectsStatsCmrcl(int $year, int $month,  TeamRepository $teamRepository, ProspectRepository $prospectRepository): Response
    {
        $teams = $teamRepository->findAll();
        $prospectsByTeam = [];

        foreach ($teams as $team) {
            $prospectsByTeam[$team->getName()] = $prospectRepository->findByMonthAndTeam($year, $month, $team->getId());
        }
        $currentYear = (int) date('Y');

        return $this->render('stat/statsCmrcl.html.twig', [
            'prospectsByTeam' => $prospectsByTeam,
            'team' =>  $teams,
            'year' => $year,
            'month' => $month,
            'currentYear' => $currentYear,
        ]);
    }
    #[Route('/prospects/tabledynmq/{year}/{month}', name: 'prospects_stats_table',)]
    public function tabledymn(int $year, int $month, ProspectRepository $prospectRepository, ProductRepository $productRepository): Response
    {
        $product = $productRepository->findProductByMonth($year, $month);


        $prospects = $prospectRepository->findProspectsByMonth($year, $month);
        $currentYear = (int) date('Y');

        return $this->render('stat/tabletest.html.twig', [

            'products' => $product,
            // dd($product),
            'prospects' => $prospects,

            'year' => $year,
            'month' => $month,
            'currentYear' => $currentYear,

        ]);
    }
    #[Route('/prospects/statstype/{year}/{month}', name: 'prospects_statype', requirements: ['year' => '\d{4}', 'month' => '\d{1,2}'])]
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
}
