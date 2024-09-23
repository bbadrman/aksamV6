<?php

namespace App\Controller;


use DateTime;
use App\Entity\Appel;
use App\Form\GsmType;
use App\Entity\Client;
use App\Entity\Cloture;
use App\Entity\History;
use App\Entity\Product;
use App\Entity\Prospect;
use App\Entity\Relanced;
use App\Form\ClientType;
use App\Form\ClotureType;
use App\Form\ProspectType;
use App\Form\RelancedType;
use App\Form\ScdEmailType;
use App\Entity\RelanceHistory;
use App\Search\SearchProspect;
use App\Form\ClientProspectType;
use App\Form\ProspectAffectType;
use App\Form\SearchProspectType;
use App\Repository\TeamRepository;
use App\Repository\AppelRepository;
use App\Repository\HistoryRepository;
use App\Repository\ProspectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Cache\CacheInterface;
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
        private AuthorizationCheckerInterface $authorizationChecker,
        private CacheInterface $cache
    ) {}
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
            $prospects = $this->prospectRepository->findByAdminNewProsp($data);
        } elseif (in_array('ROLE_TEAMALL', $roles, true)) {
            $prospects = $this->prospectRepository->findByChefAllNewProsp($data, $user);
        } elseif (in_array('ROLE_TEAM', $roles, true)) {
            $prospects = $this->prospectRepository->findByChefNewProsp($data, $user);
        } else {
            $prospects = $this->prospectRepository->findByCmrclNewProsp($data, $user);
        }

        // foreach ($prospects as $prospect) {
        //     $email = $prospect->getEmail();

        //     // Check if the email exists in the database **excluding the current prospect**
        //     $existingProspect = $this->prospectRepository->findOneBy(['email' => $email]);
        //     $isDuplicate = $existingProspect !== null && $existingProspect->getId() !== $prospect->getId();

        //     $duplicates[$email] = $isDuplicate;

        //     $emails[] = $email;
        // }


        return $this->render('prospect/index.html.twig', [
            'prospects' => $prospects,
            // 'duplicates' => $duplicates,
            'search_form' => $form->createView()
        ]);
    }

    // afficher les nouveaux prospects 
    #[Route('/newprospectchef', name: 'newprospectchef_index', methods: ['GET', 'POST'])]
    public function newprospectchef(Request $request): Response

    {
        $this->denyAccessUnlessGrantedAuthorizedRoles();

        $data = new SearchProspect();
        $data->page = $request->query->get('page', 1);
        $form = $this->createForm(SearchProspectType::class, $data);
        $form->handleRequest($this->requestStack->getCurrentRequest());



        $user = $this->security->getUser();

        $prospects = [];


        $prospects = $this->prospectRepository->findByCmrclNewProsp($data, $user);



        return $this->render('prospect/index.html.twig', [
            'prospects' => $prospects,
            // 'duplicates' => $duplicates,
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
        $roles = $user->getRoles();
        if (in_array('ROLE_SUPER_ADMIN', $roles, true) || in_array('ROLE_ADMIN', $user->getRoles(), true) || in_array('ROLE_AFFECT', $user->getRoles(), true)) {
            $prospect =  $prospectRepository->findAllNewProspectsApi(null);
        } elseif (in_array('ROLE_TEAMALL', $roles, true)) {
            $prospect =  $prospectRepository->findAllNewPanierProspectsChefApi($user, null);
        } elseif (in_array('ROLE_TEAM', $roles, true)) {
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
            // Dans 
            // $data = $form->getData();

            // if ($data->getTypeProspect() === '1') { // Si le type de prospect est professionnel
            //     $prospect->setActivites('2');
            // }

            foreach ($prospect->getProduct() as $product) {
                $product->addProspect($prospect);
            }

            $prospect->setAutor($this->getUser());
            $this->prospectRepository->add($prospect, true);

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

            $this->addFlash('success', 'Votre Prospect a été ajouté avec succès!');

            return $this->redirectToRoute('app_table_liste', [], Response::HTTP_SEE_OTHER);
        }


        return $this->renderForm('prospect/new.html.twig', [
            'prospect' => $prospect,
            'form' => $form,
        ]);
    }


    #[Route('/show/{id}', name: 'app_prospect_show', methods: ['GET', 'POST'])]
    public function show(Prospect $prospect,  Request $request,  HistoryRepository $historyRepository, AppelRepository $appelRepository)
    {
        // $user = $this->getUser();


        // // Vérifier si l'utilisateur connecté est le commercial associé au prospect
        // if ($prospect->getComrcl() !== $user) {
        //     // Si l'utilisateur n'est pas autorisé, lancer une exception AccessDeniedException
        //     throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à accéder à ce prospect.');
        // }


        $this->denyAccessUnlessGrantedAuthorizedRoles();

        // $client = HttpClient::create();


        // Utiliser la classe \DateTime de PHP
        $currentDate = new \DateTime();
        $lastDate = (clone $currentDate)->modify('-2 days');

        $data = $this->cache->get('ringover_calls_' . $prospect->getId(), function () use ($lastDate, $currentDate) {
            return $this->getRingoverCalls($lastDate, $currentDate);
        });
        //$data = $this->getRingoverCalls($lastDate, $currentDate);

        // Traiter les données reçues de Ringover 
        $this->processRingoverData($data, $appelRepository);

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

            // Iterate over all existing relances for the prospect
            foreach ($prospect->getRelanceds() as $oldRelance) {
                // Create a new RelanceHistory instance for each old relance
                $history = new RelanceHistory();
                $history->setProspect($prospect);
                $history->setMotifRelanced($oldRelance->getMotifRelanced());

                // Convert DateTime to DateTimeImmutable if needed
                $relacedAt = $oldRelance->getRelacedAt();
                $history->setRelacedAt($relacedAt instanceof \DateTimeImmutable ? $relacedAt : \DateTimeImmutable::createFromMutable($relacedAt));

                $history->setComment($oldRelance->getComment());

                // Persist the history
                $this->entityManager->persist($history);

                // Remove the old relance from the prospect and delete it
                $prospect->removeRelanced($oldRelance);
                $this->entityManager->remove($oldRelance);
                $this->entityManager->flush();
            }

            // Add the new relance to the prospect
            $prospect->addRelanced($relance);
            $this->entityManager->persist($relance);

            $this->entityManager->flush();

            $this->addFlash('success', 'Relance ajoutée avec succès.');
            //pour vider la form et rest au meme page 
            return $this->redirect($request->getRequestUri());
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
        // Associer le prospect au client
        $clientEntity->setProspect($prospect);

        // Handle the Client form submission
        $clientForm = $this->createForm(ClientProspectType::class, $clientEntity);
        $clientForm->handleRequest($request);

        //dd($clientForm);
        if ($clientForm->isSubmitted()) {
            if ($clientForm->isValid()) {
                // Vérifier si le firstName existe déjà dans la base de données
                // $existingClient = $this->entityManager->getRepository(Client::class)->findOneBy([
                //     'firstName' => $prospect->getName()
                // ]);
                // if ($existingClient) {
                //     $this->addFlash('success', 'prenon dejat.');
                // } else {

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
        // Videz ici pour Relance si ce n'est pas déjà fait
        if (!$clientForm->isSubmitted() || !$clientForm->isValid()) {
            $this->entityManager->flush();
        }
        // Trier les appels par start_time de manière décroissante
        // usort($data['call_list'], function ($a, $b) {
        //     return (new \DateTime($b['start_time'])) <=> (new \DateTime($a['start_time']));
        // });





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
            // 'formClot' => $formClot->createView(),
            'clientForm' => $clientForm->createView(),
            'gsmForm' => $gsmForm->createView(),
            'emailForm' => $emailForm->createView(),
            // 'ringoverData' => $data,
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

    private function getRingoverCalls(\DateTime $startDate, \DateTime $endDate): array
    {
        $client = HttpClient::create();

        $response = $client->request('GET', 'https://public-api.ringover.com/v2/calls', [
            'headers' => [
                'Authorization' => '926b7a524bba92932bb5f324222cb1c9f461908d',
            ],
            'query' => [
                'start_date' => $startDate->format('Y-m-d\TH:i:s.u\Z'),
                'end_date' => $endDate->format('Y-m-d\TH:i:s.u\Z'),
                'limit_count' => 400,
            ],
        ]);

        if ($response->getStatusCode() !== 200) {
            throw new \RuntimeException('Erreur lors de la récupération des données de l\'API Ringover');
        }

        return $response->toArray();
    }

    // Méthode pour traiter les données reçues de Ringover
    private function processRingoverData(array $data, AppelRepository $appelRepository): void
    {
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
                        ->setContactName($contactName)
                        ->setStartTime($startTime)
                        ->setEndTime(isset($callData['end_time']) ? new \DateTime($callData['end_time']) : null)
                        ->setDuration(isset($callData['total_duration']) ? (int)$callData['total_duration'] : null)
                        ->setRecordUrl($callData['record'] ?? null);

                    $this->entityManager->persist($appel);
                }
            }
            $this->entityManager->flush();
        }
    }
}
