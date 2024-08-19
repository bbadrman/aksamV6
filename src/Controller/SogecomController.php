<?php

namespace App\Controller;

use App\Entity\Appel;
use App\Repository\AppelRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SogecomController extends AbstractController
{

    public function __construct(
        private  EntityManagerInterface $entityManager,

    ) {}

    /**
     * @Route("/sogecom", name="sogecom-api")
     * @IsGranted("ROLE_ADMIN", message="Tu ne peut pas acces a cet ressource")
     */
    public function fetchSogecomData(SessionInterface $session): Response
    {
        $password =  $_ENV['SOGCOM_SECRET_KEY'] ?? null;
        $username = $_ENV['SOGCOM_USERNAM'] ?? null;
        $url = $_ENV['SOGCOM_URL'] ?? null;


        $client = HttpClient::create();



        $response = $client->request('POST', $url, [
            'headers' => [
                'HMAC-SHA-256' => "TNcxnc7rPzK1ax7rVOtUrQcTKtT43RlMDCVz0MHeCa52i",
                'Authorization' => "Basic " . base64_encode($username . ':' . $password),
                'Accept' => "application/json",


            ],
            'json' => [
                // "orderId" => "myOrderId-522842"
                'UUID'  => 'cccdfa39c25e4f19b37f4a69d61d5e2f'
                // 'start_date' => $lastDate->format('Y-m-d\TH:i:s.u\Z'),
                // 'end_date' => $currentDate->format('Y-m-d\TH:i:s.u\Z'),



            ]

        ]);
        // dd($response);
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
        dd($data);


        // Stocker les données dans la session
        //$session->set('sogecom_data', $data);

        // Rediriger vers la vue
        return $this->render('sogecom/view.html.twig', [
            'data' => $sogecomData,
        ]);
    }
    /**
     * @Route("/sogecom/view", name="sogecom_view")
     */
    public function viewSogecomData(SessionInterface $session): Response
    {
        // Récupérer les données de la session
        $sogecomData = $session->get('sogecom_data');

        // Renvoyer une réponse avec les données affichées dans une vue Twig
        return $this->render('sogecom/view.html.twig', [
            'data' => $sogecomData,
        ]);
    }
}
