<?php

namespace App\Controller;


use App\Form\GsmType;
use App\Entity\Client;
use App\Entity\History;
use App\Entity\Product;
use App\Entity\Prospect;
use App\Entity\Relanced;
use App\Form\ClientType;
use App\Form\ProspectType;
use App\Form\RelancedType;
use App\Search\SearchProspect;
use App\Form\ProspectAffectType;
use App\Form\SearchProspectType;
use App\Repository\TeamRepository;
use App\Repository\HistoryRepository;
use App\Repository\ProspectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
     * afficher les nouveaux prospects 
     * @Route("/newprospect", name="newprospect_index", methods={"GET", "POST"}) 
     */
    public function newprospect(Request $request,  ProspectRepository $prospectRepository,  Security $security): Response

    {
        $data = new SearchProspect();
        $data->page = $request->query->get('page', 1);
        $form = $this->createForm(SearchProspectType::class, $data);
        $form->handleRequest($this->requestStack->getCurrentRequest());
        $prospect = [];


        $user = $security->getUser();
        if (in_array('ROLE_SUPER_ADMIN', $user->getRoles(), true) || in_array('ROLE_ADMIN', $user->getRoles(), true) || in_array('ROLE_AFFECT', $user->getRoles(), true)) {
            // admin peut voire toutes les nouveaux prospects
            $prospect =  $prospectRepository->findByUserPaAffecter($data, null);
        } elseif (in_array('ROLE_TEAM', $user->getRoles(), true)) {
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
     * afficher les nouveaux prospects via API
     * @Route("/newprospectApi", name="newprospectApi_index", methods={"GET", "POST"}) 
     */
    public function newprospectApi(
        Request $request,
        ProspectRepository $prospectRepository,
        Security $security,
        SerializerInterface $serializer
    ): JsonResponse {
        $data = new SearchProspect();
        $data->page = $request->query->get('page', 1);
        $form = $this->createForm(SearchProspectType::class, $data);
        $form->handleRequest($this->requestStack->getCurrentRequest());
        $prospect = [];

        $user = $security->getUser();
        if (in_array('ROLE_SUPER_ADMIN', $user->getRoles(), true) || in_array('ROLE_ADMIN', $user->getRoles(), true) || in_array('ROLE_AFFECT', $user->getRoles(), true)) {
            // admin peut voire toutes les nouveaux prospects
            $prospect =  $prospectRepository->findAllNewProspects($data, null);
        } elseif (in_array('ROLE_TEAM', $user->getRoles(), true)) {
            // chef peut voire toutes les nouveaux prospects atacher a leur equipe
            $prospect =  $prospectRepository->findByChefAffecterApi($data,  $user, null);
        } else {
            // cmrcl peut voire seulement les nouveaux prospects atacher a lui
            $prospect =  $prospectRepository->findByCmrclAffecter($data, $user, null);
        }

        // Sérialiser les prospects
        $jsonData = $serializer->serialize($prospect, 'json');

        return new JsonResponse($jsonData, 200, [], true);
    }

    /**
     * les Relances à venir
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
            if (in_array('ROLE_SUPER_ADMIN', $user->getRoles(), true) || in_array('ROLE_ADMIN', $user->getRoles(), true)) {
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
        $productChoices = $this->entityManager->getRepository(Product::class)->createQueryBuilder('p')->getQuery()->getResult();

        $form = $this->createForm(ProspectType::class, $prospect, [
            'product_choices' => $productChoices,

        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            if ($data->getTypeProspect() === '1') { // Si le type de prospect est professionnel
                $prospect->setActivites('2');
            }


            foreach ($prospect->getProduct() as $product) {
                $product->addProspect($prospect);
            }

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
     * @Route("/show/{id}", name="app_prospect_show", methods={"GET", "POST"}) 
     */
    public function show(Prospect $prospect,  Request $request,  HistoryRepository $historyRepository)
    {
        $client = HttpClient::create();
        $response = $client->request('GET', 'https://public-api.ringover.com/v2/calls', [
            'headers' => [
                // Ajoutez ici vos en-têtes d'authentification ou d'autorisation nécessaires
                'Authorization' => '6eae1744801c7cdbdf6fbdce2b3ce4354547d1aa',
            ],
        ]);

        $data = $response->toArray();

        // Form to modify the prospect's second number
        $gsmForm = $this->createForm(GsmType::class, $prospect);
        $gsmForm->handleRequest($request);

        if ($gsmForm->isSubmitted() && $gsmForm->isValid()) {
        }

        $client = new Client();
        // //ajouter client apartir de crée contrat
        $prospectFirstName = $prospect->getName();
        $prospectLastName = $prospect->getLastName();
        $raisonSociale = $prospect->getRaisonSociale();
        $team = $prospect->getTeam();
        $cmrl = $prospect->getComrcl();
        $date =   new \DateTime();


        $client->setFirstName($prospectFirstName);
        $client->setLastName($prospectLastName);
        $client->setRaisonSociale($raisonSociale);
        $client->setTeam($team);
        $client->setCmrl($cmrl);
        $client->setCreatAt($date);


        $clientForm = $this->createForm(ClientType::class, $client);
        $clientForm->handleRequest($request);

        if ($clientForm->isSubmitted() && $clientForm->isValid()) {
            // $clientRepository->add($client, true);
            $this->entityManager->persist($client);
            $this->addFlash('success', 'client ajoutée avec succès.');
        }

        $relance = new Relanced();
        $relance->setProspect($prospect);

        $form = $this->createForm(RelancedType::class, $relance);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->entityManager->persist($relance);
            $this->addFlash('success', 'Relance ajoutée avec succès.');

            // return $this->redirectToRoute('app_prospect_show', ['id' => $prospect->getId()]);
        }
        $this->entityManager->flush();

        // $teamHistory = $this->getDoctrine()->getRepository(History::class)->findBy(['prospect' => $prospect]);
        $teamHistory = $historyRepository->findBy(['prospect' => $prospect]);


        return $this->render('prospect/show.html.twig', [
            'prospect' => $prospect,
            'teamHistory' => $teamHistory,
            'form' => $form->createView(),
            'clientForm' => $clientForm->createView(),
            'gsmForm' => $gsmForm->createView(),
            'ringoverData' => $data,
        ]);
    }

    /**
     * pour affecter 
     * @Route("/{id}/edit", name="app_prospect_edit", methods={"GET", "POST"}) 
     */
    public function edit(Request $request, Prospect $prospect, ProspectRepository $prospectRepository,  TeamRepository $teamRepository, Security $security): Response
    {

        $user = $security->getUser();

        $form = $this->createForm(ProspectAffectType::class, $prospect);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $prospectRepository->add($prospect, true);
            foreach ($prospect->getRelanceds() as $relance) {
                $relance->setProspect($prospect);
            }

            // history of prospect affect
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


            $this->entityManager->persist($teamHistory);
            $this->entityManager->flush();



            $this->addFlash('info', 'Votre Prospect a été affecté avec succès!');
            //pour reste a mon page 
            return $this->redirect($request->headers->get('referer'));

            // return $this->redirectToRoute('app_table_liste', [], Response::HTTP_SEE_OTHER);
        }
        $teams = $teamRepository->findAll();
        $team = $teamRepository->findByTeamConect($user);
        return $this->renderForm('partials/_show_modal.html.twig', [
            'prospect' => $prospect,
            'form' => $form,
            'team' => $teams,
            'teams' => $team,
        ]);
    }

    /**
     * pour edite prospect
     * @Route("/{id}/editsup", name="app_prospect_editsup", methods={"GET", "POST"}) 
     * @IsGranted("ROLE_SUPER_ADMIN", message="Tu ne peut pas acces a cet ressource") 
     */
    public function editsup(Request $request, Prospect $prospect, ProspectRepository $productRepository): Response
    {

        $productChoices = $this->entityManager->getRepository(Product::class)->createQueryBuilder('p')->getQuery()->getResult();

        $form = $this->createForm(ProspectType::class, $prospect, [
            'product_choices' => $productChoices,
        ]);


        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            foreach ($prospect->getProduct() as $product) {
                $product->addProspect($prospect);
            }

            $productRepository->add($prospect, true);
            $this->addFlash('info', 'Votre Prospect a été modifié avec succès!');
            return $this->redirect($request->headers->get('referer'));
        }

        return $this->renderForm('prospect/edit.html.twig', [
            'prospect' => $prospect,
            'form' => $form,
        ]);
    }




    /**
     * @Route("/{id}", name="app_prospect_delete", methods={"POST"}) 
     * @IsGranted("ROLE_SUPER_ADMIN", message="Tu ne peut pas acces a cet ressource") 
     */
    public function delete(Request $request, Prospect $prospect, ProspectRepository $prospectRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $prospect->getId(), $request->request->get('_token'))) {
            $prospectRepository->remove($prospect, true);
        }

        $this->addFlash('info', ' Prospect a été supprime avec succès!');
        return $this->redirect($request->headers->get('referer'));
    }
}
