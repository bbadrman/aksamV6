<?php

namespace App\Controller;

use App\Entity\Client;
use App\Form\ClientType;
use App\Search\SearchClient;
use App\Form\SearchClientType;
use App\Repository\ClientRepository;
use App\Repository\ProspectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RequestStack;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/client")  
 * @IsGranted("ROLE_USER", message="Tu ne peut pas acces a cet ressource")
 */
class ClientController extends AbstractController
{
    private $requestStack;
    private $entityManager;

    public function __construct(RequestStack $requestStack, EntityManagerInterface $entityManager)
    {

        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
    }
    /**
     * @Route("/", name="client_index", methods={"GET"})
     */
    public function index(Request $request,  ClientRepository $clientRepository,  Security $security): Response
    {
        $data = new SearchClient();
        $data->page = $request->query->get('page', 1);
        $form = $this->createForm(SearchClientType::class, $data);
        $form->handleRequest($this->requestStack->getCurrentRequest());
        $client = [];

        if ($form->isSubmitted() && $form->isValid() && !$form->isEmpty()) {

            $user = $security->getUser();
            if (in_array('ROLE_SUPER_ADMIN', $user->getRoles(), true) || in_array('ROLE_ADMIN', $user->getRoles(), true)) {
                // admi peut voire toutes les nouveaux client
                $client =  $clientRepository->findClientAdmin($data, null);
            } elseif (in_array('ROLE_TEAM', $user->getRoles(), true)) {
                // chef peut voire toutes les nouveaux client atacher a leur equipe
                $client =  $clientRepository->findClientChef($data,  $user, null);
            } else {
                // cmrcl peut voire seulement les nouveaux client atacher a lui
                $client =  $clientRepository->findClientCmrcl($data, $user, null);
            }


            return $this->render('client/index.html.twig', [
                'clients' => $client,
                'search_form' => $form->createView()
            ]);
        }
        return $this->render('client/search.html.twig', [
            'clients' => $client,
            'search_form' => $form->createView()
        ]);
    }

    /**
     * @Route("/new", name="client_new", methods={"GET", "POST"})
     * @Route("/new/{id}", name="client_new_with_id", methods={"GET", "POST"}, requirements={"id"="\d+"})
     */
    public function new(Request $request, ClientRepository $clientRepository, ProspectRepository  $prospectRepository, $id = null): Response
    {



        $client = new Client();
        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $clientRepository->add($client, true);
            $this->addFlash('success', 'Le client a été ajouté avec succès!');
            return $this->redirectToRoute('client_new', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('client/new.html.twig', [
            'client' => $client,
            'form' => $form,
        ]);
    }


    /**
     * @Route("/{id}", name="client_show", methods={"GET"})
     * @IsGranted("ROLE_USER", message="Tu ne peut pas acces a cet ressource")
     */
    public function show(Client $client): Response
    {
        return $this->render('client/show.html.twig', [
            'client' => $client,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="client_edit", methods={"GET", "POST"})
     * @IsGranted("ROLE_USER", message="Tu ne peut pas acces a cet ressource")
     */
    public function edit(Request $request, Client $client, ClientRepository $clientRepository): Response
    {
        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $clientRepository->add($client, true);
            $this->addFlash('info', 'le Client a été modifié avec succès!');
            return $this->redirectToRoute('client_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('client/edit.html.twig', [
            'client' => $client,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="client_delete", methods={"POST"}) 
     */
    public function delete(Request $request, Client $client, ClientRepository $clientRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $client->getId(), $request->request->get('_token'))) {
            $clientRepository->remove($client, true);
        }
        $this->addFlash('danger', 'le Client a été supprimé avec succès!');
        return $this->redirectToRoute('client_index', [], Response::HTTP_SEE_OTHER);
    }
}
