<?php

namespace App\Controller;


use App\Form\SearchContratCldrType;
use App\Repository\ClientRepository;
use App\Repository\ContratRepository;
use App\Search\SearchContartCalendrie;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class ContratStatController extends AbstractController
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
        private RequestStack $requestStack,
        private EntityManagerInterface $entityManager,
        private Security $security,
        private ClientRepository $clientRepository,
        private ContratRepository $contratRepository,
        private AuthorizationCheckerInterface $authorizationChecker
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


    #[Route('/', name: 'contratstat_mont', methods: ['GET'])]
    public function prospectsContratMont(): Response
    {
        $this->denyAccessUnlessGrantedAuthorizedRoles();

        $totalFrais = $this->contratRepository->getTotalFrais();

        $contratsParComrcl = $this->contratRepository->countContratsAndTotalFraisByComrclForThisMonth();
        $totauxMois = $this->contratRepository->getTotalContratsAndFraisForThisMonth(); // Ajout

        return $this->render('contrat/statmoinscontrat.html.twig', [
            'totalFrais' => $totalFrais,
            'contratsParComrcl' => $contratsParComrcl,
            'totauxMois' => $totauxMois,

        ]);
    }

    #[Route('/contratbycaldr', name: 'contrats_search', methods: ['GET', 'POST'])]
    public function searchContrats(): Response
    {
        $data = new SearchContartCalendrie();
        $form = $this->createForm(SearchContratCldrType::class, $data);
        $form->handleRequest($this->requestStack->getCurrentRequest());

        $contrats = [];
        $contratsParComrcl = [];
        $totalContrats = 0;
        $totalFrais = 0;

        if ($form->isSubmitted() && $form->isValid()) {
            $startDate = $data->getStartDate();
            $endDate = $data->getEndDate();

            // Obtenir les contrats pour la plage de dates
            $contrats = $this->contratRepository->findByDateInterval($startDate, $endDate);

            // Calculer le nombre de contrats par commercial
            $contratsParComrcl = $this->contratRepository->countContratsByComrclForInterval($startDate, $endDate);

            // Calculer le nombre total de contrats
            $totalContrats = array_sum(array_column($contratsParComrcl, 'contratCount'));

            // Calculer les frais totaux
            $totalFrais = $this->contratRepository->getTotalFraisForInterval($startDate, $endDate);
            // Calculer les frais totaux
            $totauxReglements = $this->contratRepository->getTotalReglementsForInterval($startDate, $endDate);
            $totalFirstReglm = $totauxReglements['totalFirstReglm'] ?? 0;
            $totalSecondReglm = $totauxReglements['totalSecondReglm'] ?? 0;
        }

        return $this->render('contrat/contratstat.html.twig', [
            'contrats' => $contrats,
            'contratsParComrcl' => $contratsParComrcl,
            'totalContrats' => $totalContrats,
            'totalFrais' => $totalFrais,
            // 'totalFirstReglm' => $totalFirstReglm,
            // 'totalSecondReglm' => $totalSecondReglm,
            'search_form' => $form->createView(),
        ]);
    }


    #[Route('/contrats/details/{commercial}', name: 'contrats_details', methods: ['GET'])]
    public function detailsContrats(string $commercial, Request $request): JsonResponse
    {
        $startDate = new \DateTime($request->query->get('startDate'));
        $endDate = new \DateTime($request->query->get('endDate'));

        $contrats = $this->contratRepository->findByCommercialAndInterval($commercial, $startDate, $endDate);

        return $this->json($contrats);
    }


    #[Route('/statcontratmont/detail/ajax/{id}', name: 'contratstat_mont_detail_ajax')]
    public function detailContratsCommercialAjax(int $id): JsonResponse
    {
        $contrats = $this->contratRepository->findContratsByCommercialForThisMonth($id);

        $data = [];

        foreach ($contrats as $contrat) {
            $data[] = [
                'dateSouscrpt' => $contrat->getDateSouscrpt()->format('d/m/Y'),
                'frais' => number_format($contrat->getFrais(), 2, ',', ' ') . ' €',
                'firstReglement' => number_format($contrat->getFirstReglement(), 2, ',', ' ') . ' €',
                'secondReglement' => number_format($contrat->getSecondReglement(), 2, ',', ' ') . ' €',
                'client' => $contrat->getClient()->getFirstname(), // Supposons que la méthode existe
            ];
        }

        return new JsonResponse($data);
    }
}
