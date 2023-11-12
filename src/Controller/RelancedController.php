<?php

namespace App\Controller;

use App\Entity\Relanced;
use App\Form\RelancedType;
use App\Search\SearchProspect;
use App\Form\SearchProspectType;
use App\Repository\ProspectRepository;
use App\Repository\RelancedRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;



class RelancedController extends AbstractController
{
    private $entityManager;
    private $requestStack;
    public function __construct(EntityManagerInterface $entityManager, RequestStack $requestStack)
    {
        $this->entityManager = $entityManager;
        $this->requestStack = $requestStack;
    }




    /**
     * afficher tous les relances
     * @Route("/relance", name="app_relance_index", methods={"GET"})
     */
    public function index(RelancedRepository $teamRepository, Request $request): Response
    {


        $relanced = $teamRepository->findAll();

        return $this->render('relanced/index.html.twig', [
            'relanced' => $relanced,

        ]);
    }

    /**
     * @Route("/unjoinable", name="app_unjoinable", methods={"GET", "POST"}) 
     */
    public function unjoinable(Request $request,  ProspectRepository $prospectRepository, Security $security): Response

    {
        $data = new SearchProspect();
        $data->page = $request->query->get('page', 1);
        $form = $this->createForm(SearchProspectType::class, $data);
        $form->handleRequest($this->requestStack->getCurrentRequest());
        $user = $security->getUser();

        if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
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
     * si tu veux ajouter une relance manuelle 
     * @Route("/relancetable", name="app_relance", methods={"GET", "POST"}) 
     */
    // public function relanceTable(Request $request, RelancedRepository $relancedRepository): Response
    // {
    //     $relance = new Relanced();
    //     $form = $this->createForm(RelancedType::class, $relance);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {

    //         $this->entityManager->persist($relance);
    //         $this->entityManager->flush();

    //         $relancedRepository->add($relance, true);
    //         $this->addFlash('info', 'Votre Prospect a été relancer avec succès!');
    //         return $this->redirectToRoute('app_prospect_index', [], Response::HTTP_SEE_OTHER);
    //     }

    //     return $this->renderForm('relanced/new.html.twig', [
    //         'prospect' => $relance,
    //         'form' => $form,
    //     ]);
    // }
}
