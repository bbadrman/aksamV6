<?php

namespace App\Controller;

use App\Entity\Prospect;
use App\Service\StatsService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


/**
 * @Route("/")
 * @IsGranted("ROLE_USER", message="Tu ne peut pas acces a cet ressource")
 * 
 */
class TableController extends AbstractController
{


    /**
     * @Route("/tables", name="app_table_liste", methods={"GET"})
     */
    public function new(StatsService $statsService): Response
    {
        $stats    = $statsService->getStats();

        return $this->render('prospect/table.html.twig', [
            'stats'    => $stats
        ]);
    }
}
