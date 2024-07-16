<?php

namespace App\Controller;

use App\Entity\Client;
use App\Form\ClientType;
use App\Search\SearchClient;
use App\Form\SearchClientType;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/client")  
 * @IsGranted("ROLE_USER", message="Tu ne peut pas acces a cet ressource")
 */
class ClientController extends AbstractController
{


    public function __construct(private RequestStack $requestStack, private EntityManagerInterface $entityManager, private ClientRepository $clientRepository,)
    {
    }



    /**
     * @Route("/", name="client_index", methods={"GET"})
     */
    public function index(Request $request,  Security $security): Response
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
                $client =  $this->clientRepository->findClientAdmin($data, null);
            } elseif (in_array('ROLE_TEAM', $user->getRoles(), true)) {
                // chef peut voire toutes les nouveaux client atacher a leur equipe
                $client =  $this->clientRepository->findClientChef($data,  $user, null);
            } else {
                // cmrcl peut voire seulement les nouveaux client atacher a lui
                $client =  $this->clientRepository->findClientCmrcl($data, $user, null);
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
    public function new(Request $request, $id = null): Response
    {


        $client = new Client();
        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->clientRepository->add($client, true);
            $this->addFlash('success', 'Le client a été ajouté avec succès!');
            return $this->redirectToRoute('client_new', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('client/new.html.twig', [
            'client' => $client,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/new-client", name="client_add", methods={"GET", "POST"}) 
     */
    public function add(Request $request, ValidatorInterface $validator): JsonResponse
    {

        $client = new Client();

        //$client->setName($request->get('name'));

        //$client->setDescription($request->get('description'));
        // dump($fonction);
        // die();
        $errors = $validator->validate($client);

        $errorMessages = array();

        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }
            return $this->json([
                'status' => 400,
                'errors' => $errorMessages,
            ]);
        } else {
            $this->entityManager->persist($client);
            $this->entityManager->flush();

            return $this->json([
                'status' => 200,
                'message' => 'Fonction a bien été ajouté',
            ]);
        }
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
    public function edit(Request $request, Client $client): Response
    {
        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->clientRepository->add($client, true);
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
    public function delete(Request $request, Client $client): Response
    {
        if ($this->isCsrfTokenValid('delete' . $client->getId(), $request->request->get('_token'))) {
            $this->clientRepository->remove($client, true);
        }
        $this->addFlash('danger', 'le Client a été supprimé avec succès!');
        return $this->redirectToRoute('client_index', [], Response::HTTP_SEE_OTHER);
    }
}
