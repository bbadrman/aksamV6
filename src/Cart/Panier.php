<?php

namespace App\Cart;
  
use App\Repository\ProspectRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class Panier
{
    protected $requestStack;
    public $prospectRepository;

    public function __construct(RequestStack $requestStack, ProspectRepository $prospectRepository)
    {
        $this->requestStack = $requestStack;
        $this->prospectRepository = $prospectRepository;
    }


    public function getQty(): int {
         
        $prospect =  $this->prospectRepository->findAll();
        $session = $this->requestStack->getSession();
        $qty = $session->get('prospect', count($prospect));
        return $qty;
       
         
    }
   

}

 