<?php

namespace App\Controller;

use App\Entity\Client;
use App\Form\ClientType;
use App\Repository\ClientRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/") 
 */
class ClientController extends AbstractController
{
    /**
     * @Route("/client", name="client_index", methods={"GET"})
     * @IsGranted("ROLE_USER", message="Tu ne peut pas acces a cet ressource")
     */
    public function index(ClientRepository $clientRepository): Response
    {
        return $this->render('client/index.html.twig', [
            'clients' => $clientRepository->findClientAdmin(),
        ]);
    }

    /**
     * @Route("/client/new", name="client_new", methods={"GET", "POST"})
     */
    public function new(Request $request, ClientRepository $clientRepository): Response
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
     * @Route("/client/{id}", name="client_show", methods={"GET"})
     * @IsGranted("ROLE_USER", message="Tu ne peut pas acces a cet ressource")
     */
    public function show(Client $client): Response
    {
        return $this->render('client/show.html.twig', [
            'client' => $client,
        ]);
    }

    /**
     * @Route("/client/{id}/edit", name="client_edit", methods={"GET", "POST"})
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
     * @Route("/client/{id}", name="client_delete", methods={"POST"})
     * @IsGranted("ROLE_USER", message="Tu ne peut pas acces a cet ressource")
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
