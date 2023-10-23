<?php

namespace App\Controller;

use App\Entity\Prospect;
use App\Service\StatsService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;



class TableController extends AbstractController
{


    /**
     * @Route("/tables", name="app_table_", methods={"GET"})
     */
    public function new(StatsService $statsService): Response
    {
        $stats    = $statsService->getStats();

        return $this->render('prospect/table.html.twig', [
            'stats'    => $stats
        ]);
    }
}
