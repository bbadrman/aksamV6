<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Prospect;
use App\Entity\Relanced;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;



class StatsService
{
    private $manager;
    private $security;

    public function __construct(EntityManagerInterface $manager, Security $security)
    {
        $this->manager = $manager;
        $this->security = $security;
    }
    public function getStats()
    {

        $user = $this->security->getUser();


        $users    = $this->getUsersCount();
        $teams      = $this->getTeamsCount();
        $products = $this->getProductsCount();
        $clients = $this->getClientsCount();
        $prospectsAffect = $this->getProspectCount();
        $prospectspasaffect = $this->getProspectPasCount();
        $prospectsChefNv = $this->getProspectChefNv($user);
        $prospectsCmrclNv = $this->getProspectCmrclNv($user);
        $prospectsTeam = $this->getProspectCountRelance();
        $prospectsTeamChef = $this->getProspectCountRelanceChef($user);
        $prospectsTeamCmrcl = $this->getProspectCountRelanceCmrcl($user);

        //table a venir
        $prospectsAvenir = $this->getProspectRelanceAvenir();
        $prosAvenirChef = $this->getProspectRelanceAvenirChef($user);
        $prosAvenirCmrcl = $this->getProspectRelanceAvenirCmrcl($user);


        // table prospect no traite
        $prospectsNoTraite = $this->getProspectNonTraite();
        $prospectsNoTrChef = $this->getProspectNonTraiteChef($user);
        $prospectsNoTrCmrcl = $this->getProspectNonTraiteCmrcl($user);

        // table relance no traite
        $relanceNoTraite = $this->getRelanceNonTraite();
        $relancesNoTrChef = $this->getRelanceNonTraiteChef($user);
        $relancesNoTrCmrcl = $this->getRelanceNonTraiteCmrcl($user);

        // caclcule le total du prospect en panier
        $prospectsPanier = $this->getProspectCountPanier();
        // $prospectsPanierJour = $this->getProspectCountPanierJour();

        $prospectsnow = $this->getProspectCountNow();
        $prospects = $this->getProspectTotlCount();
        $unjoiniable = $this->getProspectCountUnjoiniable();
        $unjoiniableChef = $this->getProspectCountUnjoiniableChef($user);
        $unjoiniableCmrl = $this->getProspectCountUnjoiniableCmrcl($user);

        $prospectParng = $this->getProspectParrainag();
        $prospectAppl = $this->getProspectAppl();
        $prospectAvn = $this->getProspectAvn();
        $prospectAncien = $this->getProspectAncien();
        $prospectSite = $this->getProspectSite();
        $prospectRevnd = $this->getProspectRevendeur();

        $prospectTotalTeamA = $this->getProspectTeamA($user);
        $prospectTotalTeamB = $this->getProspectTeamB($user);
        $prospectTotalTeamC = $this->getProspectTeamC($user);
        $prospectTotalTeamD = $this->getProspectTeamD($user);

        // $prospectPrngeEq = $this->getProspectParngEquipe($user);
        // $prospectAppeEq = $this->getProspectAppEquipe($user);
        // $prospectAvneEq = $this->getProspectAvnEquipe($user);
        // $prospectAutrEq = $this->getProspectAutrEquipe($user);
        // $prospectAncienEq = $this->getProspectAncienEquipe($user);
        // $prospectSiteEq = $this->getProspectSiteEquipe($user);
        // $prospectRevndEq = $this->getProspectRevendeurEquipe($user);

        // $prospectPrngeEqC = $this->getProspectParngEquipeC($user);
        // $prospectAppeEqC = $this->getProspectAppEquipeC($user);
        // $prospectAvneEqC = $this->getProspectAvnEquipeC($user);
        // $prospectAutrEqC = $this->getProspectAutrEquipeC($user);
        // $prospectAncienEqC = $this->getProspectAncienEquipeC($user);



        // $prospectPrngeEqA = $this->getProspectParngEquipeA($user);
        // $prospectAppeEqA = $this->getProspectAppEquipeA($user);
        // $prospectAvneEqA = $this->getProspectAvnEquipeA($user);
        // $prospectAutrEqA = $this->getProspectAutrEquipeA($user);



        // $prospectPrngeEqB = $this->getProspectParngEquipeB($user);
        // $prospectAppeEqB = $this->getProspectAppEquipeB($user);
        // $prospectAvneEqB = $this->getProspectAvnEquipeB($user);
        // $prospectAutrEqB = $this->getProspectAutrEquipeB($user);







        return compact('relancesNoTrCmrcl', 'relancesNoTrChef', 'relanceNoTraite', 'prosAvenirCmrcl', 'prosAvenirChef', 'prospectsAvenir', 'unjoiniableCmrl', 'unjoiniableChef', 'prospectsNoTrCmrcl', 'prospectsNoTrChef', 'prospectsTeamCmrcl', 'prospectsTeamChef', 'prospectsCmrclNv', 'prospectsChefNv', 'prospectsNoTraite', 'prospectsPanier', 'unjoiniable', 'prospects', 'prospectspasaffect', 'prospectsnow', 'prospectsTeam', 'prospectRevnd', 'prospectSite', 'prospectParng', 'prospectAppl', 'prospectAvn', 'prospectAncien',  'prospectTotalTeamA', 'prospectTotalTeamB', 'prospectTotalTeamC', 'prospectTotalTeamD',  'users', 'teams', 'products', 'clients', 'prospectsAffect');
    }


    // count number prospect for each team


    public function getProspectTeamA()
    {
        return $this->manager->createQuery('SELECT COUNT(p) FROM App\Entity\Prospect p WHERE  p.team  = 1 ')->getSingleScalarResult();
    }

    public function getProspectTeamB()
    {
        return $this->manager->createQuery('SELECT COUNT(p) FROM App\Entity\Prospect p WHERE  p.team  = 2 ')->getSingleScalarResult();
    }
    public function getProspectTeamC()
    {
        return $this->manager->createQuery('SELECT COUNT(p) FROM App\Entity\Prospect p WHERE  p.team  = 3 ')->getSingleScalarResult();
    }
    public function getProspectTeamD()
    {
        return $this->manager->createQuery('SELECT COUNT(p) FROM App\Entity\Prospect p WHERE  p.team  = 4 ')->getSingleScalarResult();
    }

    // stat du chartjs
    public function getUsersCount()
    {
        return  $this->manager->createQuery('SELECT COUNT(u) FROM App\Entity\User u')->getSingleScalarResult();
    }
    public function getTeamsCount()
    {
        return $this->manager->createQuery('SELECT COUNT(a) FROM App\Entity\Team a')->getSingleScalarResult();
    }
    public function getProductsCount()
    {
        return $this->manager->createQuery('SELECT COUNT(b) FROM App\Entity\Product b')->getSingleScalarResult();
    }
    public function getClientsCount()
    {
        return $this->manager->createQuery('SELECT COUNT(c) FROM App\Entity\Client c')->getSingleScalarResult();
    }
    public function getProspectTotlCount()
    {
        return $this->manager->createQuery('SELECT COUNT(p) FROM App\Entity\Prospect p')->getSingleScalarResult();
    }

    // les prospect cree ce jour et afficter au cmrcl  affct
    public function getProspectCount()
    {
        $today = new \DateTime();
        $today->setTime(0, 0, 0);

        $qb = $this->manager->createQueryBuilder();
        $qb->select('COUNT(p)')
            ->from('App\Entity\Prospect', 'p')
            ->where('p.team is NOT NULL')
            ->andWhere("p.comrcl is NOT NULL")
            ->andWhere('p.creatAt >= :startOfDay')
            ->setParameter('startOfDay', $today);

        $query = $qb->getQuery();
        $result = $query->getSingleScalarResult();

        return $result;
    }

    // les prospect cree ce jour et pas affc
    public function getProspectPasCount()
    {


        $qb = $this->manager->createQueryBuilder();
        $qb->select('COUNT(p)')
            ->from('App\Entity\Prospect', 'p')
            ->andWhere("p.comrcl is NULL")
            ->andWhere("p.team is NULL");


        $query = $qb->getQuery();
        $result = $query->getSingleScalarResult();

        return $result;
    }

    // les nouveaux prospects cree ce jour affecter a mon equipe
    public function getProspectChefNv(User $user)
    {
        $team = $user->getTeams();
        // $today = new \DateTime();
        // $today->setTime(0, 0, 0);

        $qb = $this->manager->createQueryBuilder();
        $qb->select('COUNT(p)')
            ->from('App\Entity\Prospect', 'p')
            ->where('p.team = :team')
            ->setParameter('team', $team)
            ->andWhere("p.comrcl is NULL");
        // ->andWhere('p.creatAt >= :startOfDay')

        // ->setParameter('startOfDay', $today);

        $query = $qb->getQuery();
        $result = $query->getSingleScalarResult();

        return $result;
    }

    // afficher les neveaux prospects au cmerciel
    public function getProspectCmrclNv($id)
    {

        $yesterday = new \DateTime('yesterday');
        $yesterday->setTime(23, 59, 59); // La fin de la journée d'hier

        $qb = $this->manager->createQueryBuilder();
        $qb->select('COUNT(p)')
            ->from('App\Entity\Prospect', 'p')
            ->andWhere('p.comrcl = :val')
            ->setParameter('val', $id);

        $qb->andWhere('p.id NOT IN ( 
                SELECT pr.id FROM App\Entity\Prospect pr
                JOIN pr.relanceds rel
                WHERE rel.relacedAt > :endOfYesterday
            )')->setParameter('endOfYesterday', $yesterday);
        // ->andWhere('p.creatAt >= :startOfDay')

        // ->setParameter('startOfDay', $today);

        $query = $qb->getQuery();
        $result = $query->getSingleScalarResult();

        return $result;
    }


    // caclcule le total du prospect relancer à venir
    public function getProspectRelanceAvenir()
    {
        $today = new \DateTime('tomorrow');
        $today->setTime(0, 0, 0);


        $qb = $this->manager->createQueryBuilder();
        $qb->select('COUNT(DISTINCT p.id)')
            ->from(Prospect::class, 'p')

            ->leftJoin('p.relanceds', 'r')
            ->andWhere('r.relacedAt >= :tomorrow')
            ->setParameter('tomorrow', $today)
            //pour count seuelement qui ont motifrlc 1 pas avec les coumun
            ->andWhere('NOT EXISTS (
                SELECT 1 FROM App\Entity\Relanced otherR
                WHERE otherR.prospect = p AND otherR.motifRelanced = 2
            )');
        $query = $qb->getQuery();
        $result = $query->getSingleScalarResult();

        return $result;
    }

    // caclcule le total du prospect relancer à venir
    public function getProspectRelanceAvenirChef(User $user)
    {
        $team = $user->getTeams();
        $today = new \DateTime('tomorrow');
        $today->setTime(0, 0, 0);

        $endOfDay = clone $today;
        $endOfDay->setTime(23, 59, 59);
        $qb = $this->manager->createQueryBuilder();
        $qb->select('COUNT(DISTINCT p.id)')
            ->from(Prospect::class, 'p')
            ->where('p.team = :team')
            ->setParameter('team', $team)
            ->leftJoin('p.relanceds', 'r')
            ->andWhere('r.relacedAt >= :tomorrow')
            ->setParameter('tomorrow', $today)
            ->andWhere('NOT EXISTS (
                SELECT 1 FROM App\Entity\Relanced otherR
                WHERE otherR.prospect = p AND otherR.motifRelanced = 2
            )');

        $query = $qb->getQuery();
        $result = $query->getSingleScalarResult();

        return $result;
    }

    // caclcule le total du prospect relancer à venir du cmrcl
    public function getProspectRelanceAvenirCmrcl($id)
    {

        $today = new \DateTime('tomorrow');
        $today->setTime(0, 0, 0);

        $endOfDay = clone $today;
        $endOfDay->setTime(23, 59, 59);
        $qb = $this->manager->createQueryBuilder();
        $qb->select('COUNT(DISTINCT p.id)')
            ->from(Prospect::class, 'p')
            ->andWhere('p.comrcl = :val')
            ->setParameter('val', $id)
            ->leftJoin('p.relanceds', 'r')
            ->andWhere('r.relacedAt >= :tomorrow')
            ->setParameter('tomorrow', $today)
            ->andWhere('NOT EXISTS (
                SELECT 1 FROM App\Entity\Relanced otherR
                WHERE otherR.prospect = p AND otherR.motifRelanced = 2
            )');

        $query = $qb->getQuery();
        $result = $query->getSingleScalarResult();

        return $result;
    }

    // caclcule le total du prospect relancer ce jour 
    public function getProspectCountRelance()
    {
        $today = new \DateTime();
        $today->setTime(0, 0, 0);

        $endOfDay = clone $today;
        $endOfDay->setTime(23, 59, 59);
        $qb = $this->manager->createQueryBuilder();
        $qb->select('COUNT(DISTINCT p.id)')
            ->from(Prospect::class, 'p')

            ->leftJoin('p.relanceds', 'r')
            ->andWhere('r.relacedAt BETWEEN :startOfDay AND :endOfDay')
            ->setParameter('startOfDay', $today)
            ->setParameter('endOfDay', $endOfDay)
            ->andWhere('r.motifRelanced = 1');


        $query = $qb->getQuery();
        $result = $query->getSingleScalarResult();

        return $result;
    }

    //calculer nombre de relance pour chef

    public function getProspectCountRelanceChef(User $user)
    {
        $team = $user->getTeams();
        $today = new \DateTime();
        $today->setTime(0, 0, 0);

        $endOfDay = clone $today;
        $endOfDay->setTime(23, 59, 59);
        $qb = $this->manager->createQueryBuilder();
        $qb->select('COUNT(DISTINCT p.id)')
            ->from(Prospect::class, 'p')
            ->where('p.team = :team')
            ->setParameter('team', $team)
            ->leftJoin('p.relanceds', 'r')
            ->andWhere('r.relacedAt BETWEEN :startOfDay AND :endOfDay')
            ->setParameter('startOfDay', $today)
            ->setParameter('endOfDay', $endOfDay)
            ->andWhere('r.motifRelanced = 1');;
        $query = $qb->getQuery();
        $result = $query->getSingleScalarResult();

        return $result;
    }
    //calculer nombre de relance pour comerciel

    public function getProspectCountRelanceCmrcl($id)
    {

        $today = new \DateTime();
        $today->setTime(0, 0, 0);

        $endOfDay = clone $today;
        $endOfDay->setTime(23, 59, 59);
        $qb = $this->manager->createQueryBuilder();
        $qb->select('COUNT(DISTINCT p.id)')
            ->from(Prospect::class, 'p')
            ->andWhere('p.comrcl = :val')
            ->setParameter('val', $id)

            ->leftJoin('p.relanceds', 'r')
            ->andWhere('r.relacedAt BETWEEN :startOfDay AND :endOfDay')
            ->setParameter('startOfDay', $today)
            ->setParameter('endOfDay', $endOfDay)
            ->andWhere('r.motifRelanced = 1');;
        $query = $qb->getQuery();
        $result = $query->getSingleScalarResult();

        return $result;
    }




    // caclcule le total du relance Non traite 
    public function getRelanceNonTraite()
    {
        $yesterday = new \DateTime('yesterday');
        $yesterday->setTime(23, 59, 59);
        // $dayBeforeYesterday = (clone $yesterday)->modify('-1 year')->setTime(0, 0, 0); // Le début d'avant-hier
        $dayBeforeYesterday = (clone $yesterday)->modify('-1 year');

        $qb = $this->manager->createQueryBuilder();
        $qb->select('COUNT(DISTINCT p.id)')  //  En utilisant COUNT(DISTINCT p.id), vous comptez les prospects uniques, en ignorant les doublons dans les relances pour chaque prospect
            ->from(Prospect::class, 'p')

            ->leftJoin('p.relanceds', 'r')

            ->Where('(r.motifRelanced = 1)') // r.motifRelanced selement = 1
            ->andWhere('r.relacedAt > :dayBeforeYesterday  ')
            ->setParameter('dayBeforeYesterday', $dayBeforeYesterday)
            ->andWhere('p.comrcl is NOT NULL')
            ->andWhere('p.team is NOT NULL');

        $qb->andWhere('p.id NOT IN (
            SELECT pr.id FROM App\Entity\Prospect pr
            JOIN pr.relanceds rel
            WHERE rel.relacedAt > :endOfYesterday
        )')->setParameter('endOfYesterday', $yesterday);


        $query = $qb->getQuery();
        $result = $query->getSingleScalarResult();

        return $result;
    }

    // caclcule le total du relance Non traite pour chef
    public function getRelanceNonTraiteChef(User $user)
    {
        $yesterday = new \DateTime('yesterday');
        $yesterday->setTime(23, 59, 59);
        $dayBeforeYesterday = (clone $yesterday)->modify('-1 year')->setTime(0, 0, 0); // Le début d'avant-hier


        $team = $user->getTeams();
        $qb = $this->manager->createQueryBuilder();
        $qb->select('COUNT(DISTINCT p.id)')
            ->from(Prospect::class, 'p')
            ->leftJoin('p.relanceds', 'r')
            ->leftJoin('p.team', 't')
            ->leftJoin('p.comrcl', 'f')
            ->where('p.team = :team')
            ->setParameter('team', $team)



            ->andWhere('(r.motifRelanced IS NULL OR r.motifRelanced = 1)')

            ->andWhere('r.relacedAt >= :dayBeforeYesterday AND r.relacedAt <= :yesterday')
            ->setParameter('dayBeforeYesterday', $dayBeforeYesterday)
            ->setParameter('yesterday', $yesterday)
            ->andWhere('p.comrcl is NOT NULL');

        $qb->andWhere('p.id NOT IN (
                SELECT pr.id FROM App\Entity\Prospect pr
                JOIN pr.relanceds rel
                WHERE rel.relacedAt > :endOfYesterday
            )')->setParameter('endOfYesterday', $yesterday);

        $query = $qb->getQuery();
        $result = $query->getSingleScalarResult();

        return $result;
    }

    // caclcule le total du relance Non traite pour comerciale
    public function getRelanceNonTraiteCmrcl($id)
    {

        $yesterday = new \DateTime('yesterday');
        $yesterday->setTime(23, 59, 59);
        $dayBeforeYesterday = (clone $yesterday)->modify('-1 month')->setTime(0, 0, 0); // Le début d'avant-hier

        $qb = $this->manager->createQueryBuilder();
        $qb->select('COUNT(DISTINCT p.id)')
            ->from(Prospect::class, 'p')
            ->andWhere('p.comrcl = :val')
            ->setParameter('val', $id)
            ->leftJoin('p.relanceds', 'r')
            ->andWhere('(r.motifRelanced IS NULL OR r.motifRelanced = 1)')

            ->andWhere('r.relacedAt >= :dayBeforeYesterday AND r.relacedAt <= :yesterday')
            ->setParameter('dayBeforeYesterday', $dayBeforeYesterday)
            ->setParameter('yesterday', $yesterday);
        $qb->andWhere('p.id NOT IN (
                SELECT pr.id FROM App\Entity\Prospect pr
                JOIN pr.relanceds rel
                WHERE rel.relacedAt > :endOfYesterday
            )')->setParameter('endOfYesterday', $yesterday);

        $query = $qb->getQuery();
        $result = $query->getSingleScalarResult();

        return $result;
    }

    // caclcule le total du prospect Non traite 
    public function getProspectNonTraite()
    {
        $now = new \DateTime();
        $yesterday = clone $now;
        $yesterday->modify('-24 hours');

        $qb = $this->manager->createQueryBuilder();
        $qb->select('COUNT(DISTINCT p.id)')
            ->from(Prospect::class, 'p')
            ->andWhere('p.team IS NOT NULL')
            ->leftJoin('p.relanceds', 'r')
            ->andWhere('r.prospect IS NULL')
            ->andWhere('p.creatAt <= :yesterday')
            ->setParameter('yesterday', $yesterday);

        $query = $qb->getQuery();
        $result = $query->getSingleScalarResult();

        return $result;
    }
    // caclcule le total du prospect Non traite pour chef
    public function getProspectNonTraiteChef(User $user)
    {
        $now = new \DateTime();
        $yesterday = clone $now;
        $yesterday->modify('-24 hours');

        $team = $user->getTeams();
        $qb = $this->manager->createQueryBuilder();
        $qb->select('COUNT(p)')
            ->from(Prospect::class, 'p')
            ->where('p.team = :team')
            ->setParameter('team', $team)
            ->leftJoin('p.relanceds', 'r')
            ->andWhere('r.prospect IS NULL')
            ->andWhere('p.team IS NOT NULL')  // chef d'equipe affecté 
            ->andWhere('p.comrcl IS NOT NULL')
            ->andWhere('p.creatAt <= :yesterday')
            ->setParameter('yesterday', $yesterday)   // Aucune relation avec relanced
        ;
        $query = $qb->getQuery();
        $result = $query->getSingleScalarResult();

        return $result;
    }

    // caclcule le total du prospect Non traite pour comerciale
    public function getProspectNonTraiteCmrcl($id)
    {
        $now = new \DateTime();
        $yesterday = clone $now;
        $yesterday->modify('-24 hours');

        $qb = $this->manager->createQueryBuilder();
        $qb->select('COUNT(p)')
            ->from(Prospect::class, 'p')
            ->andWhere('p.comrcl = :val')
            ->setParameter('val', $id)
            ->leftJoin('p.relanceds', 'r')
            ->andWhere('r.prospect IS NULL') // Aucune relation avec relanced
            // ->andWhere('p.team IS NOT NULL')  // Affecté à une équipe
            // ->andWhere('p.comrcl IS NOT NULL')
            ->andWhere('p.creatAt <= :yesterday')
            ->setParameter('yesterday', $yesterday);
        $query = $qb->getQuery();
        $result = $query->getSingleScalarResult();

        return $result;
    }


    // caclcule le total du prospect en panier
    public function getProspectCountPanier()
    {
        $today = new \DateTime();
        $today->setTime(0, 0, 0);

        $endOfDay = clone $today;
        $endOfDay->setTime(23, 59, 59);
        $qb = $this->manager->createQueryBuilder();
        $qb->select('COUNT(p)')
            ->from(Prospect::class, 'p')
            ->where('p.team is NOT NULL')
            ->andWhere("p.comrcl is NULL")
            ->andWhere('p.creatAt BETWEEN :startOfDay AND :endOfDay')
            ->setParameter('startOfDay', $today)
            ->setParameter('endOfDay', $endOfDay);
        $query = $qb->getQuery();
        $result = $query->getSingleScalarResult();

        return $result;
    }

    // caclcule le total du prospect en panier
    // public function getProspectCountPanierJour()
    // {
    //     $today = new \DateTime();
    //     $today->setTime(0, 0, 0);

    //     $endOfDay = clone $today;
    //     $endOfDay->setTime(23, 59, 59);
    //     $qb = $this->manager->createQueryBuilder();
    //     $qb->select('COUNT(p)')
    //         ->from(Prospect::class, 'p')
    //         ->leftJoin('p.histories', 'r')
    //         ->where('p.team is NOT NULL')
    //         ->andWhere("p.comrcl is NULL")

    //         ->andWhere('r.actionDate BETWEEN :startOfDay AND :endOfDay')
    //         // ->andWhere('p.creatAt BETWEEN :startOfDay AND :endOfDay')
    //         ->setParameter('startOfDay', $today)
    //         ->setParameter('endOfDay', $endOfDay);
    //     $query = $qb->getQuery();
    //     $result = $query->getSingleScalarResult();

    //     return $result;
    // }

    // caclcule le total du prospect  Unjoiniable 
    public function getProspectCountUnjoiniable()
    {

        $qb = $this->manager->createQueryBuilder();
        $qb->select('COUNT(DISTINCT p.id)')
            ->from(Prospect::class, 'p')

            ->leftJoin('p.relanceds', 'r')
            ->andWhere("r.motifRelanced = '2'");

        $query = $qb->getQuery();
        $result = $query->getSingleScalarResult();

        return $result;
    }
    // caclcule le total du prospect  Unjoiniable pour chef
    public function getProspectCountUnjoiniableChef(User $user)
    {
        $team = $user->getTeams();
        $qb = $this->manager->createQueryBuilder();
        $qb->select('COUNT(DISTINCT p.id)')
            ->from(Prospect::class, 'p')
            ->where('p.team = :team')
            ->setParameter('team', $team)
            ->leftJoin('p.relanceds', 'r')
            ->andWhere("r.motifRelanced = '2'");

        $query = $qb->getQuery();
        $result = $query->getSingleScalarResult();

        return $result;
    }

    // caclcule le total du prospect  Unjoiniable pour chef
    public function getProspectCountUnjoiniableCmrcl($id)
    {

        $qb = $this->manager->createQueryBuilder();
        $qb->select('COUNT(DISTINCT p.id)')
            ->from(Prospect::class, 'p')
            ->andWhere('p.comrcl = :val')
            ->setParameter('val', $id)
            ->leftJoin('p.relanceds', 'r')
            ->andWhere("r.motifRelanced = '2'");

        $query = $qb->getQuery();
        $result = $query->getSingleScalarResult();

        return $result;
    }


    // caclcule le total du prospect atache a une equie (panier)
    public function getProspectCountNow()
    {
        $today = new \DateTime();
        $today->setTime(0, 0, 0);

        $qb = $this->manager->createQueryBuilder();
        $qb->select('COUNT(p)')
            ->from('App\Entity\Prospect', 'p')
            ->andWhere('p.creatAt >= :startOfDay')
            ->setParameter('startOfDay', $today);

        $query = $qb->getQuery();
        $result = $query->getSingleScalarResult();

        return $result;
    }

    // Prospects en Statique Admin
    public function getProspectParrainag()
    {
        return $this->manager->createQuery('SELECT COUNT(m) FROM App\Entity\Prospect m WHERE m.motifSaise = 1  AND m.comrcl is NULL AND m.team is NULL ')->getSingleScalarResult();
    }
    public function getProspectAppl()
    {
        return $this->manager->createQuery('SELECT COUNT(m) FROM App\Entity\Prospect m WHERE m.motifSaise = 2  AND m.comrcl is NULL AND m.team is NULL ')->getSingleScalarResult();
    }
    public function getProspectAvn()
    {
        return $this->manager->createQuery('SELECT COUNT(m) FROM App\Entity\Prospect m WHERE m.motifSaise = 3 AND m.comrcl is NULL AND m.team is NULL')->getSingleScalarResult();
    }
    public function getProspectAncien()
    {
        return $this->manager->createQuery('SELECT COUNT(m) FROM App\Entity\Prospect m WHERE m.motifSaise = 4 AND m.comrcl is NULL AND m.team is NULL')->getSingleScalarResult();
    }

    public function getProspectSite()
    {
        return $this->manager->createQuery("SELECT COUNT(m) FROM App\Entity\Prospect m WHERE  m.source = 'Propre site' AND m.comrcl is NULL AND m.team is NULL")->getSingleScalarResult();
    }

    public function getProspectRevendeur()
    {
        return $this->manager->createQuery("SELECT COUNT(m) FROM App\Entity\Prospect m WHERE  m.source = 'Revendeur' AND m.comrcl is NULL AND m.team is NULL")->getSingleScalarResult();
    }



    //Prospects en Statique Chef
    // public function getProspectAvnChef(User $user){
    //     $team = $user->getTeams();

    //     return $this->manager->createQuery('SELECT COUNT(m) FROM App\Entity\Prospect m WHERE m.motifSaise = 3 AND  m.team = :team  AND m.comrcl is NULL ')->setParameter('team', $team)->getSingleScalarResult();

    // }
    // public function getProspectApplChef(User $user){
    //     $team = $user->getTeams();

    //     return $this->manager->createQuery('SELECT COUNT(m) FROM App\Entity\Prospect m WHERE m.motifSaise = 2 AND  m.team = :team  AND m.comrcl is NULL ')->setParameter('team', $team)->getSingleScalarResult();

    // }
    // public function getProspectParrngChef(User $user){
    //     $team = $user->getTeams();

    //     return $this->manager->createQuery('SELECT COUNT(m) FROM App\Entity\Prospect m WHERE m.motifSaise = 1 AND  m.team = :team  AND m.comrcl is NULL ')->setParameter('team', $team)->getSingleScalarResult();

    // }
    // public function getProspectAutreChef(User $user){
    //     $team = $user->getTeams();

    //     return $this->manager->createQuery('SELECT COUNT(m) FROM App\Entity\Prospect m WHERE m.motifSaise is NULL AND  m.team = :team  AND m.comrcl is NULL ')->setParameter('team', $team)->getSingleScalarResult();

    // }

    // test


    // Stat Affectation du  Admin 



    //Equipe 


    // public function getProspectParngEquipe()
    // {
    //     $today = new \DateTime();
    //     $today->setTime(0, 0, 0);

    //     $qb = $this->manager->createQueryBuilder();
    //     $qb->select('COUNT(p)')
    //         ->from('App\Entity\Prospect', 'p')
    //         ->where('p.motifSaise = 1')
    //         // ->andWhere('p.team = 4')

    //         ->andWhere('p.creatAt >= :startOfDay')
    //         ->setParameter('startOfDay', $today);

    //     $query = $qb->getQuery();
    //     $result = $query->getSingleScalarResult();

    //     return $result;
    // }


    // public function getProspectAppEquipe()
    // {
    //     $today = new \DateTime();
    //     $today->setTime(0, 0, 0);

    //     $qb = $this->manager->createQueryBuilder();
    //     $qb->select('COUNT(p)')
    //         ->from('App\Entity\Prospect', 'p')
    //         ->where('p.motifSaise = 2')
    //         // ->andWhere('p.team = 4')
    //         // ->andWhere('p.comrcl IS NOT NULL')
    //         ->andWhere('p.creatAt >= :startOfDay')
    //         ->setParameter('startOfDay', $today);

    //     $query = $qb->getQuery();
    //     $result = $query->getSingleScalarResult();

    //     return $result;
    // }


    // public function getProspectAvnEquipe()
    // {
    //     $today = new \DateTime();
    //     $today->setTime(0, 0, 0);

    //     $qb = $this->manager->createQueryBuilder();
    //     $qb->select('COUNT(p)')
    //         ->from('App\Entity\Prospect', 'p')
    //         ->where('p.motifSaise = 3')
    //         // ->andWhere('p.team = 4')
    //         // ->andWhere('p.comrcl IS NOT NULL')
    //         ->andWhere('p.creatAt >= :startOfDay')
    //         ->setParameter('startOfDay', $today);

    //     $query = $qb->getQuery();
    //     $result = $query->getSingleScalarResult();

    //     return $result;
    // }


    // public function getProspectAncienEquipe()
    // {
    //     $today = new \DateTime();
    //     $today->setTime(0, 0, 0);

    //     $qb = $this->manager->createQueryBuilder();
    //     $qb->select('COUNT(p)')
    //         ->from('App\Entity\Prospect', 'p')
    //         ->where('p.motifSaise = 4')
    //         // ->andWhere('p.team = 4')
    //         // ->andWhere('p.comrcl IS NOT NULL')
    //         ->andWhere('p.creatAt >= :startOfDay')
    //         ->setParameter('startOfDay', $today);

    //     $query = $qb->getQuery();
    //     $result = $query->getSingleScalarResult();

    //     return $result;
    // }

    // public function getProspectAutrEquipe()
    // {
    //     $today = new \DateTime();
    //     $today->setTime(0, 0, 0);

    //     $qb = $this->manager->createQueryBuilder();
    //     $qb->select('COUNT(p)')
    //         ->from('App\Entity\Prospect', 'p')
    //         ->where('p.motifSaise is NULL')
    //         ->andWhere('p.team = 4')
    //         ->andWhere('p.comrcl IS NOT NULL')
    //         ->andWhere('p.creatAt >= :startOfDay')
    //         ->setParameter('startOfDay', $today);

    //     $query = $qb->getQuery();
    //     $result = $query->getSingleScalarResult();

    //     return $result;
    // }


    // public function getProspectSiteEquipe()
    // {
    //     $today = new \DateTime();
    //     $today->setTime(0, 0, 0);

    //     $qb = $this->manager->createQueryBuilder();
    //     $qb->select('COUNT(p)')
    //         ->from('App\Entity\Prospect', 'p')

    //         // ->andWhere('p.team = 4')
    //         ->andWhere("p.source = 'Propre site'")
    //         // ->andWhere('p.comrcl IS NOT NULL')
    //         ->andWhere('p.creatAt >= :startOfDay')
    //         ->setParameter('startOfDay', $today);

    //     $query = $qb->getQuery();
    //     $result = $query->getSingleScalarResult();

    //     return $result;
    // }




    // public function getProspectRevendeurEquipe()
    // {
    //     $today = new \DateTime();
    //     $today->setTime(0, 0, 0);

    //     $qb = $this->manager->createQueryBuilder();
    //     $qb->select('COUNT(p)')
    //         ->from('App\Entity\Prospect', 'p')
    //         ->where('p.motifSaise is NULL')
    //         // ->andWhere('p.team = 4')
    //         ->andWhere("p.source = 'Revendeur'")
    //         // ->andWhere('p.comrcl IS NOT NULL')
    //         ->andWhere('p.creatAt >= :startOfDay')
    //         ->setParameter('startOfDay', $today);

    //     $query = $qb->getQuery();
    //     $result = $query->getSingleScalarResult();

    //     return $result;
    // }
}
