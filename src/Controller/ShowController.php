<?php

namespace App\Controller;

use App\Entity\Appel;
use App\Form\GsmType;
use App\Entity\Client;
use App\Entity\Prospect;
use App\Entity\Relanced;
use App\Form\RelancedType;
use App\Form\ScdEmailType;
use App\Entity\RelanceHistory;
use App\Form\ClientProspectType;
use App\Repository\AppelRepository;
use App\Repository\HistoryRepository;
use App\Repository\ProspectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;


/**
 * @Route("/traiter") 
 */

class ShowController extends AbstractController
{

    public function __construct(
        private  RequestStack $requestStack,
        private  EntityManagerInterface $entityManager,
        private  ProspectRepository $prospectRepository,
        private  Security $security,
        private AuthorizationCheckerInterface $authorizationChecker,
        private CacheInterface $cache
    ) {}


    #[Route('/show/{id}', name: 'app_traiter_show', methods: ['GET', 'POST'])]
    public function show(Prospect $prospect,  Request $request,  HistoryRepository $historyRepository, AppelRepository $appelRepository)
    {


        // $client = HttpClient::create();


        // // Utiliser la classe \DateTime de PHP
        // $currentDate = new \DateTime();
        // $lastDate = (clone $currentDate)->modify('-2 days');

        // $data = $this->cache->get('ringover_calls_' . $prospect->getId(), function () use ($lastDate, $currentDate) {
        //     return $this->getRingoverCalls($lastDate, $currentDate);
        // });
        // //$data = $this->getRingoverCalls($lastDate, $currentDate);

        // // Traiter les données reçues de Ringover 
        // $this->processRingoverData($data, $appelRepository);

        // Formulaires
        $emailForm = $this->createForm(ScdEmailType::class, $prospect);
        $gsmForm = $this->createForm(GsmType::class, $prospect);
        $relanceForm = $this->createForm(RelancedType::class, new Relanced());

        // Gérer les soumissions de formulaires
        $this->handleFormSubmission($emailForm, $request, $prospect);
        $this->handleFormSubmission($gsmForm, $request, $prospect);
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




        // $appel = $appelRepository->findAllOrderedByStartTime();
        //ajouter client apartir de crée client
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
                // Vérifier si le client existe déjà dans la base de données
                $qb = $this->entityManager->createQueryBuilder();
                $qb->select('c')
                    ->from(Client::class, 'c')
                    ->where('c.phone = :phone')
                    ->orWhere('c.email = :email')
                    ->orWhere('c.raisonSociale = :raisonSociale')
                    ->setParameter('phone', $prospect->getPhone())
                    ->setParameter('email', $prospect->getEmail())
                    ->setParameter('raisonSociale', $prospect->getRaisonSociale());

                $existingClient = $qb->getQuery()->getResult();

                if ($existingClient) {
                    $this->addFlash('danger', '<h1> Client déjà existant. </h1>');
                    return $this->redirect($request->getRequestUri());
                } else {
                    // Create a new Relance entity
                    foreach ($prospect->getRelanceds() as $oldRelance) {
                        $history = new RelanceHistory();
                        $history->setProspect($prospect);
                        $history->setMotifRelanced($oldRelance->getMotifRelanced());

                        $relacedAt = $oldRelance->getRelacedAt();
                        $history->setRelacedAt($relacedAt instanceof \DateTimeImmutable ? $relacedAt : \DateTimeImmutable::createFromMutable($relacedAt));
                        $history->setComment($oldRelance->getComment());

                        $this->entityManager->persist($history);

                        // Supprimer l'ancienne relance du prospect et de la base de données
                        $prospect->removeRelanced($oldRelance);
                        $this->entityManager->remove($oldRelance);
                    }
                    $relance = new Relanced();
                    $relance->setProspect($prospect);
                    $relance->setMotifRelanced('10');
                    $relance->setComment($clientForm->get('comment')->getData()); // Set a default motif relanced
                    $relance->setRelacedAt(new \DateTime()); // Set the relance date to now

                    // Add the relance to the prospect
                    $prospect->addRelanced($relance);
                    $this->entityManager->persist($relance);

                    // $this->addFlash('debug', 'Client form is valid and submitted.');

                    $this->entityManager->persist($clientEntity);
                    $this->entityManager->flush(); // Flush ici pour s'assurer que les données sont enregistrées
                    $this->addFlash('success', 'Client ajouté avec succès.');
                }
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
        // Récupérer l'historique d affectation
        $teamHistory = $historyRepository->findBy(['prospect' => $prospect]);

        return $this->render('prospect/showtest.html.twig', [
            'prospect' => $prospect,
            // 'appel' => $appel,
            'teamHistory' => $teamHistory,
            'form' => $relanceForm->createView(),
            'clientForm' => $clientForm->createView(),
            'gsmForm' => $gsmForm->createView(),
            'emailForm' => $emailForm->createView(),
            // 'ringoverData' => $data,
        ]);
    }

    private function handleFormSubmission($form, Request $request, Prospect $prospect)
    {
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($prospect);
            $this->entityManager->flush();
        }
    }

    // private function handleRelanceSubmission($form, Request $request, Prospect $prospect)
    // {
    //     $relance = new Relanced();
    //     $relance->setProspect($prospect);

    //     $form = $this->createForm(RelancedType::class, $relance);
    //     $form->handleRequest($request);
    //     if ($form->isSubmitted() && $form->isValid()) {
    //         // Iterate over all existing relances for the prospect
    //         foreach ($prospect->getRelanceds() as $oldRelance) {
    //             // Create a new RelanceHistory instance for each old relance
    //             $history = new RelanceHistory();
    //             $history->setProspect($prospect);
    //             $history->setMotifRelanced($oldRelance->getMotifRelanced());

    //             // Convert DateTime to DateTimeImmutable if needed
    //             $relacedAt = $oldRelance->getRelacedAt();
    //             $history->setRelacedAt($relacedAt instanceof \DateTimeImmutable ? $relacedAt : \DateTimeImmutable::createFromMutable($relacedAt));

    //             $history->setComment($oldRelance->getComment());

    //             // Persist the history
    //             $this->entityManager->persist($history);

    //             // Remove the old relance from the prospect and delete it
    //             $prospect->removeRelanced($oldRelance);
    //             $this->entityManager->remove($oldRelance);
    //             $this->entityManager->flush();
    //         }

    //         // Add the new relance to the prospect
    //         $prospect->addRelanced($relance);
    //         $this->entityManager->persist($relance);

    //         $this->entityManager->flush();

    //         $this->addFlash('success', 'Relance ajoutée avec succès.');
    //         //pour vider la form et rest au meme page 
    //         return $this->redirect($request->getRequestUri());
    //     }
    // }

    // #[Route('/appelspros/{id}', name: 'app_prospectpros_calls', methods: ['GET'])]
    // public function showalls(Prospect $prospect, AppelRepository $appelRepository): Response
    // {
    //     $appels = $appelRepository->findAllOrderedByStartTime();
    //     // $appels = $appelRepository->findByProspectOrderedByStartTime($prospect);

    //     return $this->render('prospect/_calls_content.html.twig', [
    //         'appels' => $appels,
    //         'prospect' => $prospect
    //     ]);
    // }


    #[Route('/appels/{id}', name: 'app_prospect_calls', methods: ['GET'])]
    public function showCalls(Prospect $prospect, AppelRepository $appelRepository): Response
    {

        $currentDate = new \DateTime();
        $lastDate = (clone $currentDate)->modify('-2 days');

        $data = $this->cache->get('ringover_calls_' . $prospect->getId(), function () use ($lastDate, $currentDate) {
            return $this->getRingoverCalls($lastDate, $currentDate);
        });
        //$data = $this->getRingoverCalls($lastDate, $currentDate);

        // Traiter les données reçues de Ringover 
        $this->processRingoverData($data, $appelRepository);

        // Récupérer les appels pour le prospect
        $appels = $appelRepository->findAllOrderedByStartTime();
        // $appels = $appelRepository->findAll();
        // $appels = $appelRepository->findByProspectOrderedByStartTime($prospect);
        // $appels = $appelRepository->findBy(['prospect' => $prospect], ['startTime' => 'DESC']);
        // $gsm = $prospect->getGsm();

        // $gsm = $prospect->getGsm();
        // $appels = $appelRepository->findByProspectGsmOrderedByStartTime($gsm);

        return $this->render('prospect/calls.html.twig', [
            'prospect' => $prospect,
            'appels' => $appels,
        ]);
        // return new JsonResponse(['html' => $html]);
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
