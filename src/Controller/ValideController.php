<?php

namespace App\Controller;

use App\Entity\Prospect;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ValideController extends AbstractController
{

    private $entityManager;


    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    /**
     * @Route("/valid", name="app_valid", methods={"GET", "POST"}) 
     */
    public function valid(Request $request, ValidatorInterface $validator): JsonResponse
    {



        $prospect = new Prospect();

        $prospect->setName($request->get('name'));
        $prospect->setLastname($request->get('lastname'));
        $prospect->setPhone($request->get('phone'));
        $prospect->setEmail($request->get('email'));
        $prospect->setGender($request->get('gender'));
        $prospect->setCity($request->get('city'));
        $prospect->setAdress($request->get('adress'));
        $brithAt = $request->get('brithAt');
        $brithAtDateTime = new \DateTime($brithAt);
        $prospect->setBrithAt($brithAtDateTime);



        $errors = $validator->validate($prospect);

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
            $this->entityManager->persist($prospect);
            $this->entityManager->flush();

            return $this->json([
                'status' => 200,
                'message' => 'Produit a bien été ajouté',
            ]);
        }
        return $this->redirectToRoute('https://www.assurance-des-vtc.fr/');
    }
}
