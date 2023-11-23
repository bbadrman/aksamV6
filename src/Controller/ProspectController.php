<?php

namespace App\Controller;

use App\Entity\Team;
use App\Form\GsmType;
use App\Entity\History;
use App\Entity\Relance;
use App\Entity\Prospect;
use App\Entity\Relanced;
use App\Form\RelanceType;
use App\Form\ProspectType;
use App\Form\RelancedType;
use App\Search\SearchProspect;
use App\Form\ProspectAffectType;
use App\Form\SearchProspectType;
use App\Form\ProspectRelanceType;
use App\Repository\RelanceRepository;
use App\Repository\ProspectRepository;
use App\Repository\RelancedRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * @Route("/prospect")
 * @IsGranted("ROLE_USER", message="Tu ne peut pas acces a cet ressource") 
 * 
 */

class ProspectController extends AbstractController
{

    private $requestStack;
    private $entityManager;

    public function __construct(RequestStack $requestStack, EntityManagerInterface $entityManager)
    {

        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/", name="app_prospect_index", methods={"GET", "POST"})  
     */
    public function index(Request $request,  ProspectRepository $prospectRepository,  Security $security): Response
    {

        $data = new SearchProspect();
        $data->page = $request->query->get('page', 1);


        $form = $this->createForm(SearchProspectType::class, $data);
        // $form->handleRequest($request);
        $form->handleRequest($this->requestStack->getCurrentRequest());

        // Pour avoir tous les prospect en taut que je suis admin 
        $user = $security->getUser();
        if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {

            // je recupere les prospects qui son pas encors affecter 

            $prospect =  $prospectRepository->findAllSearch($data, null);

            // recupere seulement les pas encour affect et cree ce jour
            $prospectpas = $prospectRepository->findByUserPaAffecter($data, null);
            // recupere qui sont relance 
            $prospectrlc = $prospectRepository->findRelanced($data,  null);
            $this->requestStack->getSession()->set('security', count($prospectpas));
            return $this->render('prospect/index.html.twig', [
                'prospects' => $prospect,
                'prospectpas' => $prospectpas,
                'prospectrlc' => $prospectrlc,
                'search_form' => $form->createView()
            ]);
        } else if (in_array('ROLE_TEAM', $user->getRoles(), true)) {

            // je recupe seulement les prospects affecter au mon equipe
            $prospect = $prospectRepository->findAllChefSearch($data, $user, null);
            $prospectChef = $prospectRepository->findByUserChefEquipe($data, $user, null);
            $prospectRelance = $prospectRepository->findByRelanceChefEquipe($data, $user, null);

            return $this->render('prospect/indexchef.html.twig', [
                'prospects' => $prospect,
                'prospectRelance' => $prospectRelance,
                'prospectChef' => $prospectChef,
                'search_form' => $form->createView()
            ]);
        }


        // Alors si je suis pas admin  je recupere selement les prospect attacher a moi 
        else {
            $prospectpas = $prospectRepository->findByUserAffecterCmrcl($data, $user, null);
            $prospect =  $prospectRepository->findByUserConect($data, $user);
            // $request->getSession()->set('security', count($prospect) );
        }

        $this->requestStack->getSession()->set('security', count($prospect));


        return $this->render('prospect/index.html.twig', [
            'prospects' => $prospect,
            'prospectpas' => $prospectpas,
            'search_form' => $form->createView()
        ]);
    }


    /**
     * afficher les nouveaux prospects 
     * @Route("/newprospect", name="newprospect_index", methods={"GET", "POST"}) 
     */
    public function newprospect(Request $request,  ProspectRepository $prospectRepository,  Security $security): Response

    {
        $data = new SearchProspect();
        $data->page = $request->query->get('page', 1);
        $form = $this->createForm(SearchProspectType::class, $data);
        $form->handleRequest($this->requestStack->getCurrentRequest());
        $user = $security->getUser();
        if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            // admi peut voire toutes les nouveaux prospects
            $prospect =  $prospectRepository->findByUserPaAffecter($data, null);
        } else if (in_array('ROLE_TEAM', $user->getRoles(), true)) {
            // chef peut voire toutes les nouveaux prospects atacher a leur equipe
            $prospect =  $prospectRepository->findByChefAffecter($data,  $user, null);
        } else {
            // cmrcl peut voire seulement les nouveaux prospects atacher a lui
            $prospect =  $prospectRepository->findByCmrclAffecter($data, $user, null);
        }


        return $this->render('prospect/index.html.twig', [
            'prospects' => $prospect,
            'search_form' => $form->createView()
        ]);
    }


    /**
     * @Route("/avenir", name="avenir_index", methods={"GET", "POST"}) 
     */
    public function avenir(Request $request,  ProspectRepository $prospectRepository,  Security $security): Response

    {
        $data = new SearchProspect();
        $data->page = $request->query->get('page', 1);
        $form = $this->createForm(SearchProspectType::class, $data);
        $form->handleRequest($this->requestStack->getCurrentRequest());



        $prospect = [];


        if ($form->isSubmitted() && $form->isValid() && !$form->isEmpty()) {

            $user = $security->getUser();
            if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
                // admi peut voire toutes les no traite
                $prospect =  $prospectRepository->findAvenir($data, null);
            } else if (in_array('ROLE_TEAM', $user->getRoles(), true)) {
                // chef peut voire toutes les no traite atacher a leur equipe
                $prospect =  $prospectRepository->findAvenirChef($data, $user, null);
            } else {
                // cmrcl peut voire seulement les no traite  atacher a lui
                $prospect =  $prospectRepository->findAvenirCmrcl($data, $user, null);
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

    /**
     * @Route("/new", name="app_prospect_new", methods={"GET", "POST"})
     */
    public function new(Request $request, ProspectRepository $prospectRepository): Response
    {
        $prospect = new Prospect();
        $form = $this->createForm(ProspectType::class, $prospect);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {



            $prospect->setAutor($this->getUser());
            $prospectRepository->add($prospect, true);

            $this->addFlash('success', 'Votre Prospect a été ajouté avec succès!');
            return $this->redirectToRoute('newprospect_index', [], Response::HTTP_SEE_OTHER);
        }


        return $this->renderForm('prospect/new.html.twig', [
            'prospect' => $prospect,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_prospect_show", methods={"GET", "POST"}) 
     */
    public function show(Request $request, Prospect $prospect)
    {
        $gsmForm = $this->createForm(GsmType::class, $prospect);
        $gsmForm->handleRequest($request);

        if ($gsmForm->isSubmitted() && $gsmForm->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
        }

        $relance = new Relanced();
        $relance->setProspect($prospect);

        $form = $this->createForm(RelancedType::class, $relance);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($relance);
            $entityManager->flush();
            $this->addFlash('success', 'Relance ajoutée avec succès.');

            return $this->redirectToRoute('app_prospect_show', ['id' => $prospect->getId()]);
        }

        $teamHistory = $this->getDoctrine()->getRepository(History::class)->findBy(['prospect' => $prospect]);

        return $this->render('prospect/show.html.twig', [
            'prospect' => $prospect,
            'teamHistory' => $teamHistory,
            'form' => $form->createView(),
            'gsmForm' => $gsmForm->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_prospect_edit", methods={"GET", "POST"}) 
     */
    public function edit(Request $request, Prospect $prospect, ProspectRepository $prospectRepository): Response
    {

        $form = $this->createForm(ProspectAffectType::class, $prospect);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $prospectRepository->add($prospect, true);
            foreach ($prospect->getRelanceds() as $fonction) {
                $fonction->setProspect($prospect);
            }

            $teamHistory = new History();
            $teamHistory->setProspect($prospect); // $prospect est votre instance de Prospect


            if ($prospect->getTeam() !== null && $prospect->getComrcl() !== null) {
                $actionType =  $prospect->getTeam()->getName() . ' et commercial ' . $prospect->getComrcl()->getUserIdentifier(); // Les deux sont associés
            } elseif ($prospect->getTeam() !== null) {
                $actionType =  $prospect->getTeam()->getName(); // Seulement associé à l'équipe
            } elseif ($prospect->getComrcl() !== null) {
                $actionType =  $prospect->getComrcl()->getUserIdentifier(); // Seulement associé au commercial
            } else {
                $actionType = 'None'; // Aucune association
            }

            $teamHistory->setActionType($actionType);

            $teamHistory->setActionDate(new \DateTime());

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($teamHistory);
            $entityManager->flush();

            $this->addFlash('info', 'Votre Prospect a été affecté avec succès!');
            return $this->redirectToRoute('app_table_liste', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('partials/_show_modal.html.twig', [
            'prospect' => $prospect,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}/relance", name="app_prospect_relance", methods={"POST"}) 
     */
    public function relance(Request $request, Relanced $relanced, RelancedRepository $relacedRepository): Response
    {
        $formR = $this->createForm(RelancedType::class, $relanced);
        $formR->handleRequest($request);

        if ($formR->isSubmitted() && $formR->isValid()) {
            $relacedRepository->add($relanced, true);

            $this->addFlash('info', 'Votre Prospect a été relancer avec succès!');
            return $this->redirectToRoute('app_prospect_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('partials/_show_relance.html.twig', [
            'relanced' => $relanced,
            'form' => $formR,
        ]);
    }


    /**
     * @Route("/{id}", name="app_prospect_delete", methods={"POST"})
     */
    public function delete(Request $request, Prospect $prospect, ProspectRepository $prospectRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $prospect->getId(), $request->request->get('_token'))) {
            $prospectRepository->remove($prospect, true);
        }

        return $this->redirectToRoute('app_prospect_index', [], Response::HTTP_SEE_OTHER);
    }
}
