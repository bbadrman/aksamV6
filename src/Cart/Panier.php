<?php

namespace App\Cart;

use App\Repository\ProspectRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class Panier
{


    public function __construct(protected RequestStack $requestStack, public ProspectRepository $prospectRepository)
    {
    }


    public function getQty(): int
    {

        $prospect =  $this->prospectRepository->findAll();
        $session = $this->requestStack->getSession();
        $qty = $session->get('prospect', count($prospect));
        return $qty;
    }
}
