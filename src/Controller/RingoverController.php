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
                'limit_count' => 1000, // Facultatif : ajuster selon les besoins
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

    /**
     * @Route("/ringoverApl", name="ringoverApl-api")
     * @IsGranted("ROLE_ADMIN", message="Tu ne peux pas accéder à cette ressource")
     */
    public function AppleRingover(AppelRepository $appelRepository, EntityManagerInterface $entityManager): Response
    {
        $client = HttpClient::create();
        $response = $client->request('GET', 'https://public-api.ringover.com/v2/calls', [
            'headers' => [
                'Authorization' => 'Bearer 6eae1744801c7cdbdf6fbdce2b3ce4354547d1aa',
            ],
        ]);

        if ($response->getStatusCode() === 200) {
            $data = $response->toArray();
            if (isset($data['call_list'])) {
                foreach ($data['call_list'] as $callData) {
                    $appel = new Appel();
                    $appel->setFromNumber($callData['from_number'])
                        ->setToNumber($callData['to_number'])
                        ->setStartTime(new \DateTime($callData['start_time']))
                        ->setEndTime(isset($callData['end_time']) ? new \DateTime($callData['end_time']) : null)
                        ->setDuration(isset($callData['total_duration']) ? (int)$callData['total_duration'] : null)
                        ->setRecordUrl($callData['record'] ?? null);

                    $entityManager->persist($appel);
                }
                $entityManager->flush();
            }
        } else {
            return new Response('Failed to fetch data from Ringover API', 500);
        }

        return new Response('Data fetched and saved successfully', 200);
    }
}
