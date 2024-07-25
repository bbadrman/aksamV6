<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegisterController extends AbstractController
{


    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    /**
     * @Route("/inscription2024@152/aksam", name="register") 
     * @IsGranted("ROLE_ADMIN", message="Tu ne peut pas acces a cet ressource")
     */
    public function index(Request $request, UserPasswordHasherInterface $encoder): Response
    {
        $success_message = null;

        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();



            $password = $encoder->hashPassword($user, $user->getPassword());
            $user->setPassword($password);

            $this->entityManager->persist($user);
            $this->entityManager->flush();
            $success_message = "Votre inscription à notre application s'est déroulée avec succès !";
        }

        return $this->render('register/register.html.twig', [
            'register_form' => $form->createView(),
            'success_message' => $success_message
        ]);
    }
}
