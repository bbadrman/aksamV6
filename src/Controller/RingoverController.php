<?php

namespace App\Controller;

use Symfony\Component\HttpClient\HttpClient;
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
        $response = $client->request('GET', 'https://public-api.ringover.com/v2/calls', [
            'headers' => [
                // Ajoutez ici vos en-têtes d'authentification ou d'autorisation nécessaires
                'Authorization' => '6eae1744801c7cdbdf6fbdce2b3ce4354547d1aa',
            ],
        ]);

        $data = $response->toArray(); // Convertit la réponse JSON en tableau associatif

        // Manipulez et utilisez les données comme nécessaire dans votre application Symfony
        // ...

        return $this->render('ringover/index.html.twig', [
            'ringoverData' => $data,

            // dd($data)
        ]);
    }
}
