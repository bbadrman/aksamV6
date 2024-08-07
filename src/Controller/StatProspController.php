<?php

namespace App\Controller;

use App\Entity\Team;
use App\Entity\Prospect;
use App\Form\SearchStatType;
use App\Search\SearchProspect;
use App\Repository\TeamRepository;
use App\Repository\ProductRepository;
use App\Repository\ProspectRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RequestStack;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


/**
 * @Route("/stats")
 * @IsGranted("ROLE_ADMIN", message="Tu ne peut pas acces a cet ressource") 
 * 
 */

class StatProspController extends AbstractController
{


    public function __construct(
        private RequestStack $requestStack,
        private TeamRepository $teamRepository,
        private ProspectRepository $prospectRepository,
        private ProductRepository $productRepository
    ) {
    }





    #[Route('/calendrie', name: 'prospects_calandri')]
    public function prospectsCalendrie(): Response
    {
        $data = new SearchProspect();
        $form = $this->createForm(SearchStatType::class, $data);
        $form->handleRequest($this->requestStack->getCurrentRequest());

        $teams = $this->teamRepository->findAll();
        $prospects = [];

        if ($form->isSubmitted() && $form->isValid() && !$form->isEmpty()) {
            $startDate = $data->getStartDate();
            $endDate = $data->getEndDate();
            $prospects = $this->prospectRepository->findByDateInterval($startDate, $endDate);
        } else {
            // Sinon, affichez tous les prospects
            $prospects = [];
        }



        return $this->render('stat/calendrie.html.twig', [
            // 'calendrie' => $calendrie,
            'prospects' => $prospects,
            'teams' => $teams,

            'search_form' => $form->createView()
        ]);
    }






    #[Route('/product/{year}/{month}', name: 'prospects_product', requirements: ['year' => '\d{4}', 'month' => '\d{1,2}'])]
    public function prospectsStatsTypeTest(int $year, int $month): Response
    {
        $products = $this->productRepository->findAll();

        // Fetch all prospects with team and product information for the month
        $prospects = $this->prospectRepository->findByMonthWithTeamAndProduct($year, $month);

        // Group prospects by team
        $prospectsByTeam = [];
        foreach ($prospects as $prospect) {
            $teamName = $prospect->getTeam()->getName();
            $product = $prospect->getProduct();  // Get the product reference first

            if ($product) { // Check if product exists before accessing its ID
                $productId = $product->getId();
                $prospectsByTeam[$teamName][$productId] = isset($prospectsByTeam[$teamName][$productId]) ? $prospectsByTeam[$teamName][$productId] + 1 : 1;
            } else {
                // Handle cases where there's no associated product (optional)
                // You can log a message, set a default value for `$productId`, etc.
            }
        }


        $currentYear = (int) date('Y');

        return $this->render('stat/statProduct.html.twig', [
            'products' => $products,
            'prospectsByTeam' => $prospectsByTeam, // Nested structure with team & product counts
            'year' => $year,
            'month' => $month,
            'currentYear' => $currentYear,
        ]);
    }
}
