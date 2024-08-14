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
    private const SECRET_KEY = 'prodpassword_kCpUKNdCppoc02eqHvCAXWbXL7wKXQM010zauxa9BXwQe'; // Remplacez par votre clé secrète

    /**
     * @Route("/sogecom", name="sogecom-api")
     * @IsGranted("ROLE_ADMIN", message="Tu ne peut pas acces a cet ressource")
     */
    public function fetchSogecomData(SessionInterface $session): Response
    {
        $client = HttpClient::create();

        // Utiliser la classe \DateTime de PHP


        $response = $client->request('GET', 'https://api-sogecommerce.societegenerale.eu', [
            'headers' => [
                'Authorization' => self::SECRET_KEY,
                'Accept' => 'application/json',

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

        // Vérifier la signature
        if (!$this->isValidSignature($data)) {
            return new Response('Invalid signature.', Response::HTTP_FORBIDDEN);
        }

        // Stocker les données dans la session
        $session->set('sogecom_data', $data);

        // Rediriger vers la vue
        return $this->redirectToRoute('sogecom_view');
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
    /**
     * Vérifie la signature des données
     */
    private function isValidSignature(array $data): bool
    {
        $supportedSignAlgos = ['sha256_hmac'];

        if (!in_array($data['kr-hash-algorithm'], $supportedSignAlgos)) {
            return false;
        }

        $krAnswer = json_encode($data['kr-answer']);
        $hash = hash_hmac('sha256', $krAnswer, self::SECRET_KEY);

        return $hash === $data['kr-hash'];
    }
}
