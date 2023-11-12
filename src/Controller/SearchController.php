<?php

namespace App\Controller;


use App\Entity\User;
use App\Search\SearchProspect;
use App\Form\SearchProspectType;
use App\Repository\ProspectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class SearchController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/search_prospect", name="prospect_search", methods={"GET"})
     * @IsGranted("ROLE_USER", message="Tu ne peut pas acces a cet ressource")
     */
    public function search(Request $request, ProspectRepository $prospectRepository, Security $security): Response
    {


        $data = new SearchProspect();
        $data->page = $request->get('page', 1);

        $form = $this->createForm(SearchProspectType::class, $data);
        $form->handleRequest($request);
        $user = $security->getUser();
        $prospect = [];


        if ($form->isSubmitted() && $form->isValid() && !$form->isEmpty()) {
            $data->page = $request->query->get('page', 1);

            if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
                // admi peut chercher toutes les prospects
                $prospect = $prospectRepository->findSearch($data, $user);
            } else if (in_array('ROLE_TEAM', $user->getRoles(), true)) {
                // chef peut chercher toutes les prospects atacher a leur equipe
                $prospect = $prospectRepository->findAllChefSearch($data, $user);
            } else {
                // cmrcl peut chercher seulement les prospects atacher a lui
                $prospect = $prospectRepository->findByUserAffecterCmrcl($data, $user, null);
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
}
