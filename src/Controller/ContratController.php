<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Contrat;
use App\Form\ContratType;
use App\Form\ContratAllType;
use App\Form\ContratEditType;
use App\Search\SearchContrat;
use App\Form\ValidContratType;
use App\Form\SearchContratType;
use App\Repository\ClientRepository;
use App\Repository\ContratRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/contrat')]
class ContratController extends AbstractController
{
    public function __construct(

        private  EntityManagerInterface $entityManager,
        private RequestStack $requestStack,

    ) {}

    #[Route('/', name: 'app_contrat_index', methods: ['GET'])]
    public function index(ContratRepository $contratRepository): Response
    {
        return $this->render('contrat/index.html.twig', [
            'contrats' => $contratRepository->findAll(),
        ]);
    }
    #[Route('/valider', name: 'app_contrat_valid_index', methods: ['GET'])]
    public function valider(Request $request, ContratRepository $contratRepository): Response
    {


        $data = new SearchContrat();
        $data->page = $request->query->get('page', 1);
        $form = $this->createForm(SearchContratType::class, $data);
        $form->handleRequest($this->requestStack->getCurrentRequest());
        $contrats = [];
        $contrats =  $contratRepository->findByContartValid($data,  null);


        if ($form->isSubmitted() && $form->isValid() && !$form->isEmpty()) {
            // admi peut voire toutes les nouveaux client
            $contrats =  $contratRepository->findByContartValid($data,  null);

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
