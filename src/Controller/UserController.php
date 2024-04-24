<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Acces;
use App\Form\UserType;
use App\Search\SearchUser;
use App\Form\SearchUserType;
use App\Repository\UserRepository;
use App\Security\UserDisconnecter;
use App\Repository\AccesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;



/**
 * @Route("/utilisateurs")
 */
class UserController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/", name="user_index", methods={"GET"})
     */
    public function index(UserRepository $userRepository, Request $request): Response
    {

        $data = new SearchUser();
        $data->page = $request->get('page', 1);

        $form = $this->createForm(SearchUserType::class, $data);
        $form->handleRequest($request);
        $users = $userRepository->findSearchUser($data);



        return $this->render('user/index.html.twig', [
            'users' => $users,

            'search_form' => $form->createView()
        ]);
    }

    /**
     * @Route("/nouveau-utilisateur", name="user_new", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN", message="Tu ne peut pas acces a cet ressource")
     */
    public function new(Request $request, UserPasswordHasherInterface $encoder): Response
    {
        $user = new User();

        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($user->getFonctions() as $fonction) {
                $fonction->addUser($user);
            }

            foreach ($user->getProducts() as $product) {
                $product->addUser($user);
            }

            // $user->setStatus(1);
            //  $data = $form->getData();
            //   dd($data);
            $password = $encoder->hashPassword($user, $user->getPassword());
            $user->setPassword($password);
            // $user->setRoles($data['roles']);

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $this->addFlash('success', 'Votre User a été ajouté avec succès!');

            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/afficher-utilisateur/{id}", name="user_show", methods={"GET"})
     */
    public function show(User $user): Response
    {


        return $this->render('user/show.html.twig', [
            'user' => $user,

        ]);
    }

    /**
     * @Route("/{id}/modifier-utilisateur", name="user_edit" )
     */
    public function edit(Request $request, User $user, UserRepository $userRepository, UserPasswordHasherInterface $encoder): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            // foreach ($user->getFonctions() as $fonction) {
            //     $fonction->addUser($user);
            // }

            $userRepository->add($user, true);
            $password = $encoder->hashPassword($user, $user->getPassword());
            $user->setPassword($password);

            //    $this->entityManager->persist($user);
            $this->entityManager->flush();
            // dd($user);
            $this->addFlash('success', 'Votre User a été modifié avec succès!');

            return $this->redirectToRoute('user_index');
        }
        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }



    /**
     * @Route("/{id}", name="user_delete", methods={"POST"})
     */
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($user);
            $this->entityManager->flush();
            $this->addFlash('danger', 'Votre User a été supprimé avec succès!');
        }

        return $this->redirectToRoute('user_index');
    }


    /**
     * @Route("/activer/{id}", name="activer")
     */

    public function activer(User $user)
    {

        $user->setStatus(($user->getStatus()) ? false : true);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return new Response("true");
    }



    /**
     * @Route("/decont/{id}", name="deconect")
     */

    public function deconect(User $user)
    {

        $user->setStatus(($user->isIsConnect()) ? false : true);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return new Response("true");
    }

    /**
     * @Route("/user/{id}/disconnect", name="user_disconnect")
     */
    public function disconnect(User $user, UserDisconnecter $userDisconnecter, Request $request): Response
    {
        // Votre logique de contrôle d'accès

        // Déconnecter l'utilisateur
        $userDisconnecter->disconnectUser($user, $request);

        return $this->redirectToRoute('user_acces');
    }


    /**
     * @Route("/user/{id}/logout", name="user_logout")
     */
    public function logoutUser(User $user): Response
    {
        // Déconnecter l'utilisateur spécifié
        // Vous pouvez utiliser le service de déconnexion personnalisé que nous avons créé précédemment
        // ou toute autre logique de déconnexion nécessaire

        return $this->redirectToRoute('user_acces');
    }
}
