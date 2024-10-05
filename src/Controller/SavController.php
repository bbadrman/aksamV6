<?php

namespace App\Controller;

use App\Entity\Sav;
use App\Form\SavType;
use App\Repository\SavRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/sav')]
class SavController extends AbstractController
{
    #[Route('/', name: 'app_sav_index', methods: ['GET'])]
    public function index(SavRepository $savRepository): Response
    {
        return $this->render('sav/index.html.twig', [
            'savs' => $savRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_sav_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $sav = new Sav();
        $form = $this->createForm(SavType::class, $sav);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($sav);
            $entityManager->flush();

            return $this->redirectToRoute('app_sav_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('sav/new.html.twig', [
            'sav' => $sav,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_sav_show', methods: ['GET'])]
    public function show(Sav $sav): Response
    {
        return $this->render('sav/show.html.twig', [
            'sav' => $sav,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_sav_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Sav $sav, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SavType::class, $sav);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_sav_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('sav/edit.html.twig', [
            'sav' => $sav,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_sav_delete', methods: ['POST'])]
    public function delete(Request $request, Sav $sav, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $sav->getId(), $request->request->get('_token'))) {
            $entityManager->remove($sav);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_sav_index', [], Response::HTTP_SEE_OTHER);
    }
}
