<?php

namespace App\Controller;


use App\Search\SearchProspect;
use App\Form\SearchProspectType;
use App\Repository\ProspectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class SearchController extends AbstractController
{



    public function __construct(
        private EntityManagerInterface $entityManager,
        private ProspectRepository $prospectRepository,
        private Security $security
    ) {
    }


    /**
     * Search for all prospects
     * @Route("/search_prospect", name="prospect_search", methods={"GET"})
     * @IsGranted("ROLE_USER", message="Tu ne peut pas acces a cet ressource")
     */
    public function search(Request $request): Response
    {


        $data = new SearchProspect();
        $data->page = $request->get('page', 1);

        $form = $this->createForm(SearchProspectType::class, $data);
        $form->handleRequest($request);

        $user = $this->security->getUser();
        $roles = $user->getRoles();
        $prospect = [];




        if ($form->isSubmitted() && $form->isValid() && !$form->isEmpty()) {

            if (in_array('ROLE_SUPER_ADMIN',  $roles, true) || in_array('ROLE_ADMIN',  $roles, true) || in_array('ROLE_AFFECT',  $roles, true)) {
                // admi peut chercher toutes les prospects
                $prospect = $this->prospectRepository->findSearch($data, $user);
            } else if (in_array('ROLE_TEAM',  $roles, true)) {
                // chef peut chercher toutes les prospects atacher a leur equipe
                $prospect = $this->prospectRepository->findAllChefSearch($data, $user);
            } elseif (in_array('ROLE_USER',  $roles, true)) {
                // cmrcl peut chercher seulement les prospects atacher a lui
                $prospect = $this->prospectRepository->findByUserAffecterCmrcl($data, $user);
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
