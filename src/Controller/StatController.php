<?php

namespace App\Controller;


use App\Repository\TeamRepository;
use App\Repository\UserRepository;
use App\Repository\ProspectRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StatController extends AbstractController
{
    #[Route('/stat', name: 'app_stat')]
    public function index(TeamRepository $teamRepository): Response
    {
        $team = $teamRepository->findAll();
        return $this->render('stat/index.html.twig', [
            'team' => $team,
        ]);
    }

    #[Route('/statcomrcl', name: 'app_stat_comrcl')]
    public function comrcl(ProspectRepository $prospectRepository): Response
    {
        $prospects = $prospectRepository->findAll();
        return $this->render('stat/comrcl.html.twig', [
            'prospects' => $prospects,
        ]);
    }
}
