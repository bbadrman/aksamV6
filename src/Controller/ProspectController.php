<?php

namespace App\Controller;


use DateTime;
use App\Entity\Appel;
use App\Form\GsmType;
use App\Entity\Client;
use App\Entity\History;
use App\Entity\Product;
use App\Entity\Prospect;
use App\Entity\Relanced;
use App\Form\ClientType;
use App\Form\ProspectType;
use App\Form\RelancedType;
use App\Form\ScdEmailType;
use App\Search\SearchProspect;
use App\Form\ProspectAffectType;
use App\Form\SearchProspectType;
use App\Repository\AppelRepository;
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
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * @Route("/prospect") 
 */

class ProspectController extends AbstractController
{

    private const AUTHORIZED_ROLES = [
        'ROLE_ADMIN',
        'ROLE_TEAM',
        'ROLE_AFFECT',
        'ROLE_ADD_PROS',
        'ROLE_EDIT_PROS',
        'ROLE_PROS',
        'ROLE_COMERC'
    ];

    public function __construct(
        private  RequestStack $requestStack,
        private  EntityManagerInterface $entityManager,
        private  ProspectRepository $prospectRepository,
        private  Security $security,
        private AuthorizationCheckerInterface $authorizationChecker
    ) {
    }
    private function denyAccessUnlessGrantedAuthorizedRoles(): void
    {
        if (!$this->getUser()) {
            throw new AccessDeniedException("Accès refusé pour les utilisateurs anonymes");
        }

        foreach (self::AUTHORIZED_ROLES as $role) {
            if ($this->authorizationChecker->isGranted($role)) {
                return;
            }
        }

        throw new AccessDeniedException("Tu ne peux pas accéder à cette ressource");
    }


    // afficher les nouveaux prospects 
    #[Route('/newprospect', name: 'newprospect_index', methods: ['GET', 'POST'])]
    public function newprospect(Request $request): Response

    {
        $this->denyAccessUnlessGrantedAuthorizedRoles();

        $data = new SearchProspect();
        $data->page = $request->query->get('page', 1);
        $form = $this->createForm(SearchProspectType::class, $data);
        $form->handleRequest($this->requestStack->getCurrentRequest());



        $user = $this->security->getUser();
        $roles = $user->getRoles();
        $prospects = [];

        if (in_array('ROLE_SUPER_ADMIN', $roles, true) || in_array('ROLE_ADMIN', $roles, true) || in_array('ROLE_AFFECT', $roles, true)) {
            $prospects = $this->prospectRepository->findByUserPaAffecter($data);
        } elseif (in_array('ROLE_TEAM', $roles, true)) {
            $prospects = $this->prospectRepository->findByChefAffecter($data, $user);
        } else {
            $prospects = $this->prospectRepository->findByCmrclAffecter($data, $user);
        }



        return $this->render('prospect/index.html.twig', [
            'prospects' => $prospects,
            'search_form' => $form->createView()
        ]);
    }


    /**
     * Afficher les nouveaux prospects via API return Int
     * @Route("/newprospectApi", name="newprospectApi_index", methods={"GET"}) 
     */

    public function newprospectApi(
        ProspectRepository $prospectRepository,
        Security $security,
        SerializerInterface $serializer
    ): JsonResponse {

        $this->denyAccessUnlessGrantedAuthorizedRoles();

        $prospect = [];
        $user = $security->getUser();
        if (in_array('ROLE_SUPER_ADMIN', $user->getRoles(), true) || in_array('ROLE_ADMIN', $user->getRoles(), true) || in_array('ROLE_AFFECT', $user->getRoles(), true)) {
            $prospect =  $prospectRepository->findAllNewProspectsApi(null);
        } elseif (in_array('ROLE_TEAM', $user->getRoles(), true)) {
            // chef peut voire toutes les nouveaux prospects atacher a leur equipe
            $prospect =  $prospectRepository->findAllNewProspectsChefApi($user, null);
        } else {
            // cmrcl peut voire seulement les nouveaux prospects atacher a lui
            $prospect =  $prospectRepository->findAllNewProspectsComercialApi($user, null);
        }


        // Sérialiser les prospects
        $jsonData = $serializer->serialize($prospect, 'json');

        return new JsonResponse($jsonData, 200, [], true);
    }


    /**
     * les Relances à venir 
     */
    #[Route('/avenir', name: 'avenir_index', methods: ['GET', 'POST'])]
    public function avenir(Request $request,    Security $security): Response

    {
        $this->denyAccessUnlessGrantedAuthorizedRoles();


        $data = new SearchProspect();
        $data->page = $request->query->get('page', 1);
        $form = $this->createForm(SearchProspectType::class, $data);
        $form->handleRequest($this->requestStack->getCurrentRequest());

        $user = $this->security->getUser();
        $roles = $user->getRoles();


        $prospect = [];
        if ($form->isSubmitted() && $form->isValid() && !$form->isEmpty()) {

            $user = $security->getUser();
            if (in_array('ROLE_SUPER_ADMIN', $roles, true) || in_array('ROLE_ADMIN', $roles, true)) {
                // admi peut voire toutes les no traite
                $prospect =  $this->prospectRepository->findAvenir($data, null);
            } else if (in_array('ROLE_TEAM', $roles, true)) {
                // chef peut voire toutes les no traite atacher a leur equipe
                $prospect =  $this->prospectRepository->findAvenirChef($data, $user, null);
            } else {
                // cmrcl peut voire seulement les no traite  atacher a lui
                $prospect =  $this->prospectRepository->findAvenirCmrcl($data, $user, null);
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



    #[Route('/new', name: 'app_prospect_new', methods: ['GET', 'POST'])]

    public function new(Request $request,): Response
    {
        $this->denyAccessUnlessGrantedAuthorizedRoles();

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
            $this->prospectRepository->add($prospect, true);

            $this->addFlash('success', 'Votre Prospect a été ajouté avec succès!');
            return $this->redirectToRoute('newprospect_index', [], Response::HTTP_SEE_OTHER);
        }


        return $this->renderForm('prospect/new.html.twig', [
            'prospect' => $prospect,
            'form' => $form,
        ]);
    }


    #[Route('/show/{id}', name: 'app_prospect_show', methods: ['GET', 'POST'])]
    public function show(Prospect $prospect,  Request $request,  HistoryRepository $historyRepository, AppelRepository $appelRepository)
    {
        $this->denyAccessUnlessGrantedAuthorizedRoles();

        $client = HttpClient::create();


        // Utiliser la classe \DateTime de PHP
        $currentDate = new \DateTime();
        $lastDate = (clone $currentDate)->modify('-15 days');

        $response = $client->request('GET', 'https://public-api.ringover.com/v2/calls', [
            'headers' => [
                // Ajoutez ici vos en-têtes d'authentification ou d'autorisation nécessaires
                'Authorization' => '926b7a524bba92932bb5f324222cb1c9f461908d',
            ],
            'query' => [
                'start_date' => $lastDate->format('Y-m-d\TH:i:s.u\Z'),
                'end_date' => $currentDate->format('Y-m-d\TH:i:s.u\Z'),
                'limit_count' => 1000, // Facultatif : ajuster selon les besoins


            ],

        ]);
        if ($response->getStatusCode() !== 200) {
            // Gérer les erreurs HTTP
            return new Response('Erreur lors de la récupération des données de l\'API Ringover', $response->getStatusCode());
        }


        $data = $response->toArray();

        if (isset($data['call_list'])) {
            foreach ($data['call_list'] as $callData) {
                $contactName = $callData['user']['concat_name'] ?? null;
                $startTime = new \DateTime($callData['start_time']);
                $existingCall = $appelRepository->findByUniqueProperties(
                    $callData['from_number'],
                    $callData['to_number'],
                    $startTime
                );

                if (!$existingCall) {
                    $appel = new Appel();
                    $appel->setFromNumber($callData['from_number'])
                        ->setToNumber($callData['to_number'])
                        ->setContactName($contactName)  // Corrected this line
                        ->setStartTime($startTime)
                        ->setEndTime(isset($callData['end_time']) ? new \DateTime($callData['end_time']) : null)
                        ->setDuration(isset($callData['total_duration']) ? (int)$callData['total_duration'] : null)
                        ->setRecordUrl($callData['record'] ?? null);

                    $this->entityManager->persist($appel);
                }
            }
            $this->entityManager->flush();
        }


        // Form to modify the prospect's second email
        $emailForm = $this->createForm(ScdEmailType::class, $prospect);
        $emailForm->handleRequest($request);

        if ($emailForm->isSubmitted() && $emailForm->isValid()) {
            $this->entityManager->persist($prospect);
            $this->entityManager->flush();
        }
        // Form to modify the prospect's second number
        $gsmForm = $this->createForm(GsmType::class, $prospect);
        $gsmForm->handleRequest($request);

        if ($gsmForm->isSubmitted() && $gsmForm->isValid()) {
            $this->entityManager->persist($prospect);
            $this->entityManager->flush();
        }





        // Gerer les relance 
        $relance = new Relanced();
        $relance->setProspect($prospect);

        $form = $this->createForm(RelancedType::class, $relance);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->entityManager->persist($relance);
            $this->addFlash('success', 'Relance ajoutée avec succès.');

            // return $this->redirectToRoute('app_prospect_show', ['id' => $prospect->getId()]);
        }

        // //ajouter client apartir de crée contrat
        $clientEntity = new Client();
        $clientEntity->setFirstName($prospect->getName());
        $clientEntity->setLastName($prospect->getLastName());
        $clientEntity->setPhone($prospect->getPhone());
        $clientEntity->setEmail($prospect->getEmail());
        $clientEntity->setRaisonSociale($prospect->getRaisonSociale());
        $clientEntity->setTeam($prospect->getTeam());
        $clientEntity->setCmrl($prospect->getComrcl());
        $clientEntity->setCreatAt(new \DateTime());


        // Handle the Client form submission
        $clientForm = $this->createForm(ClientType::class, $clientEntity);
        $clientForm->handleRequest($request);

        //dd($clientForm);
        if ($clientForm->isSubmitted()) {
            if ($clientForm->isValid()) {
                // Debugging output
                $this->addFlash('debug', 'Client form is valid and submitted.');

                $this->entityManager->persist($clientEntity);
                $this->entityManager->flush(); // Flush ici pour s'assurer que les données sont enregistrées
                $this->addFlash('success', 'Client ajouté avec succès.');
            } else {
                // Debugging output
                $this->addFlash('debug', 'Client form is submitted but not valid.');

                // Display errors
                $errors = $clientForm->getErrors(true, false);
                foreach ($errors as $error) {
                    $this->addFlash('error', $error->getMessage());
                }
            }
        }
        // Flush here for Relance if not already flushed
        if (!$clientForm->isSubmitted() || !$clientForm->isValid()) {
            $this->entityManager->flush();
        }
        // Trier les appels par start_time de manière décroissante
        usort($data['call_list'], function ($a, $b) {
            return (new \DateTime($b['start_time'])) <=> (new \DateTime($a['start_time']));
        });





        //$this->entityManager->flush();

        // $teamHistory = $this->getDoctrine()->getRepository(History::class)->findBy(['prospect' => $prospect]);
        $teamHistory = $historyRepository->findBy(['prospect' => $prospect]);
        //$appel = $appelRepository->findAll();
        $appel = $appelRepository->findAllOrderedByStartTime();



        return $this->render('prospect/show.html.twig', [
            'prospect' => $prospect,
            'appel' => $appel,
            'teamHistory' => $teamHistory,
            'form' => $form->createView(),
            'clientForm' => $clientForm->createView(),
            'gsmForm' => $gsmForm->createView(),
            'emailForm' => $emailForm->createView(),
            'ringoverData' => $data,
        ]);
    }

    /**
     * pour affecter 
     * @Route("/{id}/edit", name="app_prospect_edit", methods={"GET", "POST"}) 
     */
    public function edit(Request $request, Prospect $prospect,   TeamRepository $teamRepository): Response
    {

        $this->denyAccessUnlessGrantedAuthorizedRoles();

        $user = $this->security->getUser();

        $form = $this->createForm(ProspectAffectType::class, $prospect);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->prospectRepository->add($prospect, true);
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
    public function editsup(Request $request, Prospect $prospect): Response
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

            $this->prospectRepository->add($prospect, true);
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
