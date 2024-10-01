<?php

namespace App\Controller;

use \DateTime;
use App\Entity\Appel;
use App\Repository\AppelRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RingoverController extends AbstractController
{

    public function __construct(
        private  EntityManagerInterface $entityManager,
    ) {}

    /**
     * @Route("/ringover", name="ringover-api")
     * @IsGranted("ROLE_ADMIN", message="Tu ne peut pas acces a cet ressource")
     */
    public function fetchRingoverData(): Response
    {
        $client = HttpClient::create();

        // Utiliser la classe \DateTime de PHP
        $currentDate = new \DateTime();
        $lastDate = (clone $currentDate)->modify('-15 days');

        $response = $client->request('GET', 'https://public-api.ringover.com/v2/calls', [
            'headers' => [
                'Authorization' => '926b7a524bba92932bb5f324222cb1c9f461908d', // Assurez-vous du format
            ],
            'query' => [
                'start_date' => $lastDate->format('Y-m-d\TH:i:s.u\Z'),
                'end_date' => $currentDate->format('Y-m-d\TH:i:s.u\Z'),
                'limit_count' => 00, // Facultatif : ajuster selon les besoins
            ],
        ]);

        // Vérifiez si la requête a échoué et affichez le message d'erreur
        if ($response->getStatusCode() !== 200) {
            $statusCode = $response->getStatusCode();
            $errorContent = $response->getContent(false); // Ne pas lancer d'exception pour les codes d'erreur HTTP
            $headers = $response->getHeaders(false);

            // Rendre les détails de l'erreur pour le débogage
            return new Response(
                "<h1>Error</h1>" .
                    "<p>Status Code: $statusCode</p>" .
                    "<p>Content: $errorContent</p>" .
                    "<p>Headers: " . json_encode($headers) . "</p>",
                $statusCode
            );
        }

        $data = $response->toArray(); // Convertit la réponse JSON en tableau associatif

        return $this->render('ringover/index.html.twig', [
            'ringoverData' => $data,
        ]);
    }


    function stockerRingoverDb(AppelRepository $appelRepository)
    {
        $currentDate = new \DateTime();
        $lastDate = (clone $currentDate)->modify('-15 days');

        $data = $this->getRingoverCalls($lastDate, $currentDate);

        // Traiter les données reçues de Ringover 
        $this->processRingoverData($data, $appelRepository);
    }

    /**
     * @Route("/{id}/getApplShow", name="get_appl_show") 
     */
    function getCallsShow(AppelRepository $appelRepository,  Request $request): Response
    {

        $appel = $appelRepository->findAllOrderedByStartTime();


        return $this->render('partials/_show_apl.html.twig', [
            'appel' => $appel,
        ]);
    }

    /**
     * @Route("/ringoverApl", name="ringoverApl-api")
     * @IsGranted("ROLE_ADMIN", message="Tu ne peux pas accéder à cette ressource")
     */
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
                'limit_count' => 100,
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
