<?php


namespace App\Service;

use App\Entity\Appel;
use App\Repository\AppelRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class RingoverService
{

    public function __construct(
        private  EntityManagerInterface $entityManager,
    ) {
    }
    function getRingoverCalls(\DateTime $startDate, \DateTime $endDate): array
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
    function processRingoverData(array $data, AppelRepository $appelRepository): void
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
