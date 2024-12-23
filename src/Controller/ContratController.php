<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Contrat;
use App\Form\ContratType;
use App\Form\ContratAllType;
use App\Form\ContratEditType;
use App\Form\SearchContratCldrType;
use App\Search\SearchContrat;
use App\Form\ValidContratType;
use App\Form\SearchContratType;
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
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

#[Route('/contrat')]
class ContratController extends AbstractController
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

    #[Route('/', name: 'app_contrat_index', methods: ['GET'])]
    public function index(Request $request, ContratRepository $contratRepository): Response
    {
        $data = new SearchContrat();
        $data->page = $request->query->get('page', 1);
        $form = $this->createForm(SearchContratType::class, $data);
        $form->handleRequest($this->requestStack->getCurrentRequest());
        $contrats = [];
        if ($form->isSubmitted() && $form->isValid() && !$form->isEmpty()) {
            $contrats =  $contratRepository->findAll($data);
        }

        return $this->render('contrat/indextest.html.twig', [
            'contrats' => $contrats,
            'search_form' => $form->createView()
        ]);
    }
    #[Route('/valider', name: 'app_contrat_valid_index', methods: ['GET'])]
    public function valider(Request $request, ContratRepository $contratRepository): Response
    {

        $this->denyAccessUnlessGrantedAuthorizedRoles();
        $data = new SearchContrat();
        $data->page = $request->query->get('page', 1);
        $form = $this->createForm(SearchContratType::class, $data);
        $form->handleRequest($this->requestStack->getCurrentRequest());
        $contrats = [];


        if ($form->isSubmitted() && $form->isValid() && !$form->isEmpty()) {
            $user = $this->security->getUser();
            if (in_array('ROLE_SUPER_ADMIN', $user->getRoles(), true)  || in_array('ROLE_ADMIN', $user->getRoles(), true)) {
                // admi peut voire toutes les nouveaux client
                $contrats =  $contratRepository->findByContartValid($data,  null);
            } elseif (in_array('ROLE_VALIDE', $user->getRoles(), true)) {
                // Rôle spécifique pour ROLE_VALIDE
                $contrats = $contratRepository->findByContartValid($data,  $user, null);
            } elseif (in_array('ROLE_TEAM', $user->getRoles(), true)) {
                // chef peut voire toutes les nouveaux client atacher a leur equipe
                $contrats =  $contratRepository->findByContartValid($data, $user,  null);
            } else {
                // cmrcl peut voire seulement les nouveaux client atacher a lui
                $contrats =  $contratRepository->findByContartValidComrcl($data, $user, null);
            }



            return $this->render('contrat/index.html.twig', [
                'contrats' => $contrats,
                'search_form' => $form->createView()
            ]);
        }
        return $this->render('contrat/search.html.twig', [
            'contrats' => $contrats,
            'search_form' => $form->createView()
        ]);
    }

    #[Route('/new/{id}', name: 'app_contrat_new', methods: ['GET', 'POST'])]
    public function new(int $id, Request $request, EntityManagerInterface $entityManager, ClientRepository $clientRepository): Response
    {

        $client = $clientRepository->find($id);
        if (!$client) {
            throw $this->createNotFoundException('Client not found');
        }
        $contrat = new Contrat();
        $contrat->setNom($client->getLastName());
        $contrat->setPrenom($client->getFirstName());
        $contrat->setRaisonSociale($client->getRaisonSociale());
        $contrat->setClient($client);

        $form = $this->createForm(ContratType::class, $contrat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // foreach ($contrat->getProduct() as $product) {
            //     $product->set($contrat);
            // }

            $contrat->setComrcl($this->getUser());

            $entityManager->persist($contrat);
            $entityManager->flush();

            return $this->redirectToRoute('app_contrat_valid_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('contrat/new.html.twig', [
            'contrat' => $contrat,
            'form' => $form,

        ]);
    }

    #[Route('/newcontrat/{id}', name: 'add_contrat_new', methods: ['GET', 'POST'])]
    public function add(int $id, Request $request, EntityManagerInterface $entityManager, ClientRepository $clientRepository): Response
    {

        $client = $clientRepository->find($id);
        if (!$client) {
            throw $this->createNotFoundException('Client not found');
        }
        $contrat = new Contrat();
        $contrat->setNom($client->getLastName());
        $contrat->setPrenom($client->getFirstName());
        $contrat->setRaisonSociale($client->getRaisonSociale());
        $contrat->setClient($client);

        $form = $this->createForm(ContratAllType::class, $contrat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {



            $entityManager->persist($contrat);
            $entityManager->flush();

            return $this->redirectToRoute('app_contrat_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('contrat/new.html.twig', [
            'contrat' => $contrat,
            'form' => $form,

        ]);
    }



    #[Route('/stat', name: 'contrats_search', methods: ['GET', 'POST'])]
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
            $totalFirstReglm = $this->contratRepository->getTotalFirstReglmForInterval($startDate, $endDate);
        }

        return $this->render('contrat/contratstat.html.twig', [
            'contrats' => $contrats,
            'contratsParComrcl' => $contratsParComrcl,
            'totalContrats' => $totalContrats,
            'totalFrais' => $totalFrais,
            // 'totalFirstReglm' => $totalFirstReglm,
            'search_form' => $form->createView(),
        ]);
    }



    #[Route('/{id}', name: 'app_contrat_show', methods: ['GET', 'POST'])]
    public function show(Request $request, Contrat $contrat): Response
    {
        // Form to modify the prospect's second email
        $form = $this->createForm(ValidContratType::class, $contrat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($contrat);
            $this->entityManager->flush();
            return $this->redirectToRoute('app_contrat_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('contrat/show.html.twig', [
            'contrat' => $contrat,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_contrat_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Contrat $contrat, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ContratEditType::class, $contrat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_contrat_valid_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('contrat/edit.html.twig', [
            'contrat' => $contrat,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_contrat_delete', methods: ['POST'])]
    public function delete(Request $request, Contrat $contrat, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $contrat->getId(), $request->request->get('_token'))) {
            $entityManager->remove($contrat);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_contrat_index', [], Response::HTTP_SEE_OTHER);
    }
}
