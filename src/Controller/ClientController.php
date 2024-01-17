<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Prospect;
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
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/client") 
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
     * @IsGranted("ROLE_USER", message="Tu ne peut pas acces a cet ressource")
     */
    public function index(Request $request,  ClientRepository $clientRepository,  Security $security): Response
    {
        $data = new SearchClient();
        $data->page = $request->query->get('page', 1);
        $form = $this->createForm(SearchClientType::class, $data);
        $form->handleRequest($this->requestStack->getCurrentRequest());
        $client = [];

        $user = $security->getUser();
        if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
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

    /**
     * @Route("/new", name="client_new", methods={"GET", "POST"})
     * @Route("/new/{id}", name="client_new_with_id", methods={"GET", "POST"}, requirements={"id"="\d+"})
     */
    public function new(Request $request, ClientRepository $clientRepository, ProspectRepository  $prospectRepository, $id = null): Response
    {

        $prospect = null;
        $client = new Client();

        //ajouter client apartir de crée contrat

        // $raisonSociale = $prospect->getRaisonSociale();
        // $team = $prospect->getTeam();
        // $cmrl = $prospect->getComrcl();
        $date =   new \DateTime();

        if ($id !== null) {
            $prospect = $prospectRepository->find($id);

            if ($prospect !== null) {
                // Update the Client with the Prospect information
                $prospectFirstName = $prospect->getName();
                $prospectLastName = $prospect->getLastName();

                // Your existing code...

                // Set the Client first and last name based on Prospect
                $client->setFirstName($prospectFirstName ?? 'DefaultFirstName');
                $client->setLastName($prospectLastName ?? 'DefaultLastName');

                // Your existing code...
            }
        }




        // $client->setRaisonSociale($raisonSociale);
        // $client->setTeam($team);
        // $client->setCmrl($cmrl);
        $client->setCreatAt($date);



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
     * @Route("/add", name="client_add", methods={"GET", "POST"})
     */
    public function addClient(Request $request, ClientRepository $clientRepository): Response
    {
        // $prospect = $this->getDoctrine()->getRepository(Prospect::class)->find($id);
        $prospect = new Prospect();
        $client = new Client();

        //ajouter client apartir de crée contrat
        $prospectFirstName = $prospect->getName();
        $prospectLastName = $prospect->getLastName();
        $raisonSociale = $prospect->getRaisonSociale();
        $team = $prospect->getTeam();
        $cmrl = $prospect->getComrcl();
        $date =   new \DateTime();

        if ($prospectFirstName == null && $prospectLastName == null) {
            $client->setFirstName($prospectFirstName);
            $client->setLastName($prospectLastName);
        } else {
            // Handle the case where $prospectFirstName is null (depending on your business logic)
            // For example, you might set a default value or log a warning.
            $client->setFirstName('DefaultFirstName');
            $client->setLastName('DefaultFirstName');

            // or log a warning: $this->logger->warning('Prospect first name is null for client creation.');
        }
        $client->setRaisonSociale($raisonSociale);
        $client->setTeam($team);
        $client->setCmrl($cmrl);
        $client->setCreatAt($date);


        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $clientRepository->add($client, true);
            foreach ($client->getProspect() as $prospect) {
                $prospect->setProspect($client);
            }
            // $this->entityManager->persist($client);
            $this->entityManager->flush();
            $this->addFlash('success', 'Le client a été ajouté avec succès!');
        }

        return $this->renderForm('partials/_modal_client.html.twig', [
            'client' => $client,
            'form' => $form,
            'prospect' => $prospect
        ]);
    }

    // /**
    //  * @Route("/new-client", name="clients_add", methods={"GET", "POST"}) 
    //  */
    // public function addjson(Request $request, ValidatorInterface $validator): JsonResponse
    // {

    //     $client = new Client();

    //     // $client->setFirstName($request->get('firstname'));

    //     // $client->setLastName($request->get('lastname'));
    //     // $client->setPhone($request->get('phone'));
    //     // $client->setEmail($request->get('email'));
    //     // $client->setRaisonSociale($request->get('raisonSociale'));

    //     $firstname = $request->get('firstname');
    //     $lastName = $request->get('lastname');
    //     $phone = $request->get('phone');
    //     $email = $request->get('email');
    //     $raisonSociale = $request->get('raisonSociale');
    //     if ($firstname !== null) {
    //         $client->setFirstName($firstname);
    //         $client->setLastName($lastName);
    //         $client->setPhone($phone);
    //         $client->setEmail($email);
    //         $client->setRaisonSociale($raisonSociale);
    //     } else {
    //         return $this->json([
    //             'status' => 400,
    //             'errors' => ['Firstname cannot be null.'],
    //         ]);
    //     }

    //     // dump($fonction);
    //     // die();
    //     $errors = $validator->validate($client);

    //     $errorMessages = array();

    //     if (count($errors) > 0) {
    //         foreach ($errors as $error) {
    //             $errorMessages[] = $error->getMessage();
    //         }
    //         return $this->json([
    //             'status' => 400,
    //             'errors' => $errorMessages,
    //         ]);
    //     } else {
    //         $this->entityManager->persist($client);
    //         $this->entityManager->flush();

    //         return $this->json([
    //             'status' => 200,
    //             'message' => 'Fonction a bien été ajouté',
    //         ]);
    //     }
    // }

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
