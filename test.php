<?php 

#[Route('/show/{id}', name: 'app_prospect_show', methods: ['GET', 'POST'])]
    public function show(Prospect $prospect,  Request $request,  HistoryRepository $historyRepository)
    {
         

        //Gerer les relances
        $relance = new Relanced();
        $relance->setProspect($prospect);

        $form = $this->createForm(RelancedType::class, $relance);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->entityManager->persist($relance);
            $this->addFlash('success', 'Relance ajoutée avec succès.');
   }

        // //ajouter client apartir de crée contrat
        $clientEntity = new Client();
        $clientEntity->setFirstName($prospect->getName());
        $clientEntity->setLastName($prospect->getLastName());
        $clientEntity->setRaisonSociale($prospect->getRaisonSociale());
        $clientEntity->setTeam($prospect->getTeam());
        $clientEntity->setCmrl($prospect->getComrcl());
        $clientEntity->setCreatAt(new \DateTime());


        // Handle the Client form submission
        $clientForm = $this->createForm(ClientType::class, $clientEntity);
        $clientForm->handleRequest($request);

        //dd($clientForm);
        if ($clientForm->isSubmitted() && $clientForm->isValid()) {
            // Debugging output
            $this->addFlash('debug', 'Client form is valid and submitted.');

            $this->entityManager->persist($clientEntity);

            $this->addFlash('success', 'Client ajouté avec succès.');
        } else {
            $errors = $clientForm->getErrors(true, false);
            foreach ($errors as $error) {
                $this->addFlash('error', $error->getMessage());
            }
        }

       
        $this->entityManager->flush();

        // $teamHistory = $this->getDoctrine()->getRepository(History::class)->findBy(['prospect' => $prospect]);
        $teamHistory = $historyRepository->findBy(['prospect' => $prospect]);


        return $this->render('prospect/show.html.twig', [
            'prospect' => $prospect,
            'teamHistory' => $teamHistory,
            'form' => $form->createView(),
            'clientForm' => $clientForm->createView(), 
        ]);
    }

    $query = $this->createQueryBuilder('p')
    ->select('p, t, f')
    ->leftJoin('p.team', 't')
    ->leftJoin('p.comrcl', 'f')
    ->where('p.team IN (:teams) ')
    ->setParameter('teams', $team)
    ->andWhere('p.team IS NOT NULL')
    ->andWhere('p.comrcl IS NULL OR p.comrcl = :val') // Filtrer les prospects no affectés et affect au chef aussi
    ->setParameter('val', $user)
    ->orderBy('p.id', 'DESC');