<?php

namespace App\Controller;

use App\Entity\Transaction;
use App\Form\TransactionType;
use App\Search\SearchTransaction;
use App\Form\SearchTransactionType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\TransactionRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RequestStack;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

#[Route('/transaction')]
class TransactionController extends AbstractController
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
        private AuthorizationCheckerInterface $authorizationChecker,
        private TransactionRepository $transactionRepository,
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
    #[Route('/', name: 'app_transaction_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $this->denyAccessUnlessGrantedAuthorizedRoles();

        $data = new SearchTransaction();
        $data->page = $request->get('page', 1);
        $form = $this->createForm(SearchTransactionType::class, $data);
        $form->handleRequest($this->requestStack->getCurrentRequest());

        $transaction = [];

        if ($form->isSubmitted() && $form->isValid() && !$form->isEmpty()) {
            $transaction = $this->transactionRepository->findSearchTransaction($data, null);


            return $this->render('transaction/index.html.twig', [
                'transactions' => $transaction,
                'search_form' => $form->createView()

            ]);
        }
        return $this->render('transaction/search.html.twig', [
            'transactions' => $transaction,
            'search_form' => $form->createView()

        ]);
    }

    #[Route('/new', name: 'app_transaction_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGrantedAuthorizedRoles();
        $transaction = new Transaction();
        $form = $this->createForm(TransactionType::class, $transaction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($transaction);
            $entityManager->flush();

            return $this->redirectToRoute('app_transaction_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('transaction/new.html.twig', [
            'transaction' => $transaction,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_transaction_show', methods: ['GET'])]
    public function show(Transaction $transaction): Response
    {
        $this->denyAccessUnlessGrantedAuthorizedRoles();
        return $this->render('transaction/show.html.twig', [
            'transaction' => $transaction,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_transaction_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Transaction $transaction, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGrantedAuthorizedRoles();
        $form = $this->createForm(TransactionType::class, $transaction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_transaction_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('transaction/edit.html.twig', [
            'transaction' => $transaction,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_transaction_delete', methods: ['POST'])]
    public function delete(Request $request, Transaction $transaction, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGrantedAuthorizedRoles();
        if ($this->isCsrfTokenValid('delete' . $transaction->getId(), $request->request->get('_token'))) {
            $entityManager->remove($transaction);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_transaction_index', [], Response::HTTP_SEE_OTHER);
    }
}
