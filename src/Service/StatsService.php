<?php

namespace App\Service;

use App\Entity\User;
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
        $prospectsPasAffect = $this->getProspectPasCount();
        $prospectsTeam = $this->getProspectCountTeam();
        $prospectsnow = $this->getProspectCountNow();
        $prospects = $this->getProspectTotlCount();

        $prospectParng = $this->getProspectParrainag();
        $prospectAppl = $this->getProspectAppl();
        $prospectAvn = $this->getProspectAvn();
        $prospectAncien = $this->getProspectAncien();
        $prospectSite = $this->getProspectSite();
        $prospectRevnd = $this->getProspectRevendeur();
        // $prospectAutre = $this->getProspectAutre(); 
        // $prospectAvnChef = $this->getProspectAvnChef($user);
        // $prospectApplChef = $this->getProspectApplChef($user);
        // $prospectParrngChef = $this->getProspectParrngChef($user);
        // $prospectAutreChef = $this->getProspectAutreChef($user); 

        $prospectTotalTeamA = $this->getProspectTeamA($user);
        $prospectTotalTeamB = $this->getProspectTeamB($user);
        $prospectTotalTeamC = $this->getProspectTeamC($user);
        $prospectTotalTeamD = $this->getProspectTeamD($user);

        $prospectPrngeEq = $this->getProspectParngEquipe($user);
        $prospectAppeEq = $this->getProspectAppEquipe($user);
        $prospectAvneEq = $this->getProspectAvnEquipe($user);
        $prospectAutrEq = $this->getProspectAutrEquipe($user);
        $prospectAncienEq = $this->getProspectAncienEquipe($user);
        $prospectSiteEq = $this->getProspectSiteEquipe($user);
        $prospectRevndEq = $this->getProspectRevendeurEquipe($user);

        $prospectPrngeEqC = $this->getProspectParngEquipeC($user);
        $prospectAppeEqC = $this->getProspectAppEquipeC($user);
        $prospectAvneEqC = $this->getProspectAvnEquipeC($user);
        $prospectAutrEqC = $this->getProspectAutrEquipeC($user);
        $prospectAncienEqC = $this->getProspectAncienEquipeC($user);
        $prospectSiteEqC = $this->getProspectSiteEquipeC($user);
        $prospectRevndEqC = $this->getProspectRevendeurEquipeC($user);

        $prospectPrngeEqA = $this->getProspectParngEquipeA($user);
        $prospectAppeEqA = $this->getProspectAppEquipeA($user);
        $prospectAvneEqA = $this->getProspectAvnEquipeA($user);
        $prospectAutrEqA = $this->getProspectAutrEquipeA($user);
        $prospectAncienEqA = $this->getProspectAnncEquipeA($user);
        $prospectSiteEqA = $this->getProspectSiteEquipeA($user);
        $prospectRevndEqA = $this->getProspectRevendeurEquipeA($user);

        $prospectPrngeEqB = $this->getProspectParngEquipeB($user);
        $prospectAppeEqB = $this->getProspectAppEquipeB($user);
        $prospectAvneEqB = $this->getProspectAvnEquipeB($user);
        $prospectAutrEqB = $this->getProspectAutrEquipeB($user);
        $prospectAncienEqB = $this->getProspectAncienEquipeB($user);
        $prospectSiteEqB = $this->getProspectSiteEquipeB($user);
        $prospectRevndEqB = $this->getProspectRevendeurEquipeB($user);

        $prospectTestEqB = $this->getProspectTestChef($user);
        $prospectTetChef = $this->getProspectTetChef($user);



        return compact('prospects', 'prospectsPasAffect', 'prospectsnow', 'prospectsTeam', 'prospectRevnd', 'prospectSite', 'prospectParng', 'prospectAppl', 'prospectAvn', 'prospectAncien',  'prospectTotalTeamA', 'prospectTotalTeamB', 'prospectTotalTeamC', 'prospectTotalTeamD', 'prospectRevndEqC', 'prospectSiteEqC', 'prospectRevndEqB', 'prospectSiteEqB', 'prospectRevndEq', 'prospectSiteEq', 'prospectRevndEqA', 'prospectSiteEqA', 'prospectAncienEq', 'prospectAncienEqC', 'prospectAncienEqB', 'prospectAncienEqA', 'prospectTestEqB', 'prospectTetChef', 'prospectAutrEqB', 'prospectAvneEqB', 'prospectAppeEqB', 'prospectPrngeEqB', 'prospectAutrEqA', 'prospectAvneEqA', 'prospectAppeEqA', 'prospectPrngeEqA', 'prospectAutrEqC', 'prospectAvneEqC', 'prospectAppeEqC', 'prospectPrngeEqC', 'prospectAutrEq', 'prospectAvneEq', 'prospectAppeEq', 'prospectPrngeEq', 'users', 'teams', 'products', 'clients', 'prospectsAffect');
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
    // caclcule le total du prospect atache a une equie (panier)
    public function getProspectCountTeam()
    {
        $today = new \DateTime();
        $today->setTime(0, 0, 0);

        $qb = $this->manager->createQueryBuilder();
        $qb->select('COUNT(p)')
            ->from('App\Entity\Prospect', 'p')
            ->andWhere('p.relacedAt >= :startOfDay')
            ->setParameter('startOfDay', $today);

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
    public function getProspectTestChef(User $user)
    {
        $team = $user->getTeams();

        return $this->manager->createQuery('SELECT COUNT(m) FROM App\Entity\Prospect m WHERE m.motifSaise is NULL AND  m.team = :team  AND m.comrcl = 4')->setParameter('team', $team)->getSingleScalarResult();
    }

    public function getProspectTetChef(User $user)
    {
        $team = $user->getTeams();

        return $this->manager->createQuery('SELECT COUNT(m) FROM App\Entity\Prospect m WHERE m.motifSaise is NULL AND  m.team = :team  AND m.comrcl = 6')->setParameter('team', $team)->getSingleScalarResult();
    }

    // Stat Affectation du  Admin 



    //Equipe A

    public function getProspectParngEquipeA()
    {
        $today = new \DateTime();
        $today->setTime(0, 0, 0);

        $qb = $this->manager->createQueryBuilder();
        $qb->select('COUNT(p)')
            ->from('App\Entity\Prospect', 'p')
            ->where('p.motifSaise = 1')
            ->andWhere('p.team = 1')
            ->andWhere('p.comrcl IS NOT NULL')
            ->andWhere('p.creatAt >= :startOfDay')
            ->setParameter('startOfDay', $today);

        $query = $qb->getQuery();
        $result = $query->getSingleScalarResult();

        return $result;
    }


    public function getProspectAppEquipeA()
    {
        $today = new \DateTime();
        $today->setTime(0, 0, 0);

        $qb = $this->manager->createQueryBuilder();
        $qb->select('COUNT(p)')
            ->from('App\Entity\Prospect', 'p')
            ->where('p.motifSaise = 2')
            ->andWhere('p.team = 1')
            ->andWhere('p.comrcl IS NOT NULL')
            ->andWhere('p.creatAt >= :startOfDay')
            ->setParameter('startOfDay', $today);

        $query = $qb->getQuery();
        $result = $query->getSingleScalarResult();

        return $result;
    }

    public function getProspectAvnEquipeA()
    {
        $today = new \DateTime();
        $today->setTime(0, 0, 0);

        $qb = $this->manager->createQueryBuilder();
        $qb->select('COUNT(p)')
            ->from('App\Entity\Prospect', 'p')
            ->where('p.motifSaise = 3')
            ->andWhere('p.team = 1')
            ->andWhere('p.comrcl IS NOT NULL')
            ->andWhere('p.creatAt >= :startOfDay')
            ->setParameter('startOfDay', $today);

        $query = $qb->getQuery();
        $result = $query->getSingleScalarResult();

        return $result;
    }


    public function getProspectAnncEquipeA()
    {
        $today = new \DateTime();
        $today->setTime(0, 0, 0);

        $qb = $this->manager->createQueryBuilder();
        $qb->select('COUNT(p)')
            ->from('App\Entity\Prospect', 'p')
            ->where('p.motifSaise = 4')
            ->andWhere('p.team = 1')
            ->andWhere('p.comrcl IS NOT NULL')
            ->andWhere('p.creatAt >= :startOfDay')
            ->setParameter('startOfDay', $today);

        $query = $qb->getQuery();
        $result = $query->getSingleScalarResult();

        return $result;
    }

    public function getProspectAutrEquipeA()
    {
        $today = new \DateTime();
        $today->setTime(0, 0, 0);

        $qb = $this->manager->createQueryBuilder();
        $qb->select('COUNT(p)')
            ->from('App\Entity\Prospect', 'p')
            ->where('p.motifSaise is NULL')
            ->andWhere('p.team = 1')
            ->andWhere('p.comrcl IS NOT NULL')
            ->andWhere('p.creatAt >= :startOfDay')
            ->setParameter('startOfDay', $today);

        $query = $qb->getQuery();
        $result = $query->getSingleScalarResult();

        return $result;
    }

    public function getProspectSiteEquipeA()
    {
        $today = new \DateTime();
        $today->setTime(0, 0, 0);

        $qb = $this->manager->createQueryBuilder();
        $qb->select('COUNT(p)')
            ->from('App\Entity\Prospect', 'p')
            ->where('p.motifSaise is NULL')
            ->andWhere('p.team = 1')
            ->andWhere("p.source = 'Propre site'")
            ->andWhere('p.comrcl IS NOT NULL')
            ->andWhere('p.creatAt >= :startOfDay')
            ->setParameter('startOfDay', $today);

        $query = $qb->getQuery();
        $result = $query->getSingleScalarResult();

        return $result;
    }

    public function getProspectRevendeurEquipeA()
    {
        $today = new \DateTime();
        $today->setTime(0, 0, 0);

        $qb = $this->manager->createQueryBuilder();
        $qb->select('COUNT(p)')
            ->from('App\Entity\Prospect', 'p')
            ->where('p.motifSaise is NULL')
            ->andWhere('p.team = 1')
            ->andWhere("p.source = 'Revendeur'")
            ->andWhere('p.comrcl IS NOT NULL')
            ->andWhere('p.creatAt >= :startOfDay')
            ->setParameter('startOfDay', $today);

        $query = $qb->getQuery();
        $result = $query->getSingleScalarResult();

        return $result;
    }

    //Equipe B

    public function getProspectParngEquipeB()
    {
        $today = new \DateTime();
        $today->setTime(0, 0, 0);

        $qb = $this->manager->createQueryBuilder();
        $qb->select('COUNT(p)')
            ->from('App\Entity\Prospect', 'p')
            ->where('p.motifSaise = 1')
            ->andWhere('p.team = 2')
            ->andWhere('p.comrcl IS NOT NULL')
            ->andWhere('p.creatAt >= :startOfDay')
            ->setParameter('startOfDay', $today);

        $query = $qb->getQuery();
        $result = $query->getSingleScalarResult();

        return $result;
    }



    public function getProspectAppEquipeB()
    {
        $today = new \DateTime();
        $today->setTime(0, 0, 0);

        $qb = $this->manager->createQueryBuilder();
        $qb->select('COUNT(p)')
            ->from('App\Entity\Prospect', 'p')
            ->where('p.motifSaise = 2')
            ->andWhere('p.team = 2')
            ->andWhere('p.comrcl IS NOT NULL')
            ->andWhere('p.creatAt >= :startOfDay')
            ->setParameter('startOfDay', $today);

        $query = $qb->getQuery();
        $result = $query->getSingleScalarResult();

        return $result;
    }


    public function getProspectAvnEquipeB()
    {
        $today = new \DateTime();
        $today->setTime(0, 0, 0);

        $qb = $this->manager->createQueryBuilder();
        $qb->select('COUNT(p)')
            ->from('App\Entity\Prospect', 'p')
            ->where('p.motifSaise = 3')
            ->andWhere('p.team = 2')
            ->andWhere('p.comrcl IS NOT NULL')
            ->andWhere('p.creatAt >= :startOfDay')
            ->setParameter('startOfDay', $today);

        $query = $qb->getQuery();
        $result = $query->getSingleScalarResult();

        return $result;
    }

    public function getProspectAutrEquipeB()
    {
        $today = new \DateTime();
        $today->setTime(0, 0, 0);

        $qb = $this->manager->createQueryBuilder();
        $qb->select('COUNT(p)')
            ->from('App\Entity\Prospect', 'p')
            ->where('p.motifSaise is NULL')
            ->andWhere('p.team = 2')
            ->andWhere('p.comrcl IS NOT NULL')
            ->andWhere('p.creatAt >= :startOfDay')
            ->setParameter('startOfDay', $today);

        $query = $qb->getQuery();
        $result = $query->getSingleScalarResult();

        return $result;
    }


    public function getProspectAncienEquipeB()
    {
        $today = new \DateTime();
        $today->setTime(0, 0, 0);

        $qb = $this->manager->createQueryBuilder();
        $qb->select('COUNT(p)')
            ->from('App\Entity\Prospect', 'p')
            ->where('p.motifSaise = 4')
            ->andWhere('p.team = 2')
            ->andWhere('p.comrcl IS NOT NULL')
            ->andWhere('p.creatAt >= :startOfDay')
            ->setParameter('startOfDay', $today);

        $query = $qb->getQuery();
        $result = $query->getSingleScalarResult();

        return $result;
    }


    public function getProspectSiteEquipeB()
    {
        $today = new \DateTime();
        $today->setTime(0, 0, 0);

        $qb = $this->manager->createQueryBuilder();
        $qb->select('COUNT(p)')
            ->from('App\Entity\Prospect', 'p')
            ->where('p.motifSaise  is NULL')
            ->andWhere('p.team = 2')
            ->andWhere("p.source = 'Propre site'")
            ->andWhere('p.comrcl IS NOT NULL')
            ->andWhere('p.creatAt >= :startOfDay')
            ->setParameter('startOfDay', $today);

        $query = $qb->getQuery();
        $result = $query->getSingleScalarResult();

        return $result;
    }



    public function getProspectRevendeurEquipeB()
    {
        $today = new \DateTime();
        $today->setTime(0, 0, 0);

        $qb = $this->manager->createQueryBuilder();
        $qb->select('COUNT(p)')
            ->from('App\Entity\Prospect', 'p')
            ->where('p.motifSaise  is NULL')
            ->andWhere('p.team = 2')
            ->andWhere("p.source = 'Revendeur'")
            ->andWhere('p.comrcl IS NOT NULL')
            ->andWhere('p.creatAt >= :startOfDay')
            ->setParameter('startOfDay', $today);

        $query = $qb->getQuery();
        $result = $query->getSingleScalarResult();

        return $result;
    }

    //Equipe C

    public function getProspectParngEquipeC()
    {
        $today = new \DateTime();
        $today->setTime(0, 0, 0);

        $qb = $this->manager->createQueryBuilder();
        $qb->select('COUNT(p)')
            ->from('App\Entity\Prospect', 'p')
            ->where('p.motifSaise = 1')
            ->andWhere('p.team = 3')
            ->andWhere('p.comrcl IS NOT NULL')
            ->andWhere('p.creatAt >= :startOfDay')
            ->setParameter('startOfDay', $today);

        $query = $qb->getQuery();
        $result = $query->getSingleScalarResult();

        return $result;
    }




    public function getProspectAppEquipeC()
    {
        $today = new \DateTime();
        $today->setTime(0, 0, 0);

        $qb = $this->manager->createQueryBuilder();
        $qb->select('COUNT(p)')
            ->from('App\Entity\Prospect', 'p')
            ->where('p.motifSaise = 2')
            ->andWhere('p.team = 3')
            ->andWhere('p.comrcl IS NOT NULL')
            ->andWhere('p.creatAt >= :startOfDay')
            ->setParameter('startOfDay', $today);

        $query = $qb->getQuery();
        $result = $query->getSingleScalarResult();

        return $result;
    }



    public function getProspectAvnEquipeC()
    {
        $today = new \DateTime();
        $today->setTime(0, 0, 0);

        $qb = $this->manager->createQueryBuilder();
        $qb->select('COUNT(p)')
            ->from('App\Entity\Prospect', 'p')
            ->where('p.motifSaise = 3')
            ->andWhere('p.team = 3')
            ->andWhere('p.comrcl IS NOT NULL')
            ->andWhere('p.creatAt >= :startOfDay')
            ->setParameter('startOfDay', $today);

        $query = $qb->getQuery();
        $result = $query->getSingleScalarResult();

        return $result;
    }


    public function getProspectAutrEquipeC()
    {
        $today = new \DateTime();
        $today->setTime(0, 0, 0);

        $qb = $this->manager->createQueryBuilder();
        $qb->select('COUNT(p)')
            ->from('App\Entity\Prospect', 'p')
            ->where('p.motifSaise is NULL')
            ->andWhere('p.team = 3')
            ->andWhere('p.comrcl IS NOT NULL')
            ->andWhere('p.creatAt >= :startOfDay')
            ->setParameter('startOfDay', $today);

        $query = $qb->getQuery();
        $result = $query->getSingleScalarResult();

        return $result;
    }


    public function getProspectAncienEquipeC()
    {
        $today = new \DateTime();
        $today->setTime(0, 0, 0);

        $qb = $this->manager->createQueryBuilder();
        $qb->select('COUNT(p)')
            ->from('App\Entity\Prospect', 'p')
            ->where('p.motifSaise = 4')
            ->andWhere('p.team = 3')
            ->andWhere('p.comrcl IS NOT NULL')
            ->andWhere('p.creatAt >= :startOfDay')
            ->setParameter('startOfDay', $today);

        $query = $qb->getQuery();
        $result = $query->getSingleScalarResult();

        return $result;
    }

    public function getProspectSiteEquipeC()
    {
        $today = new \DateTime();
        $today->setTime(0, 0, 0);

        $qb = $this->manager->createQueryBuilder();
        $qb->select('COUNT(p)')
            ->from('App\Entity\Prospect', 'p')
            ->where('p.motifSaise is NULL ')
            ->andWhere('p.team = 3')
            ->andWhere("p.source = 'Propre site'")
            ->andWhere('p.comrcl IS NOT NULL')
            ->andWhere('p.creatAt >= :startOfDay')
            ->setParameter('startOfDay', $today);

        $query = $qb->getQuery();
        $result = $query->getSingleScalarResult();

        return $result;
    }

    public function getProspectRevendeurEquipeC()
    {
        $today = new \DateTime();
        $today->setTime(0, 0, 0);

        $qb = $this->manager->createQueryBuilder();
        $qb->select('COUNT(p)')
            ->from('App\Entity\Prospect', 'p')
            ->where('p.motifSaise is NULL ')
            ->andWhere('p.team = 3')
            ->andWhere("p.source = 'Revendeur'")
            ->andWhere('p.comrcl IS NOT NULL')
            ->andWhere('p.creatAt >= :startOfDay')
            ->setParameter('startOfDay', $today);

        $query = $qb->getQuery();
        $result = $query->getSingleScalarResult();

        return $result;
    }
    //Equipe D


    public function getProspectParngEquipe()
    {
        $today = new \DateTime();
        $today->setTime(0, 0, 0);

        $qb = $this->manager->createQueryBuilder();
        $qb->select('COUNT(p)')
            ->from('App\Entity\Prospect', 'p')
            ->where('p.motifSaise = 1')
            ->andWhere('p.team = 4')
            ->andWhere('p.comrcl IS NOT NULL')
            ->andWhere('p.creatAt >= :startOfDay')
            ->setParameter('startOfDay', $today);

        $query = $qb->getQuery();
        $result = $query->getSingleScalarResult();

        return $result;
    }


    public function getProspectAppEquipe()
    {
        $today = new \DateTime();
        $today->setTime(0, 0, 0);

        $qb = $this->manager->createQueryBuilder();
        $qb->select('COUNT(p)')
            ->from('App\Entity\Prospect', 'p')
            ->where('p.motifSaise = 2')
            ->andWhere('p.team = 4')
            ->andWhere('p.comrcl IS NOT NULL')
            ->andWhere('p.creatAt >= :startOfDay')
            ->setParameter('startOfDay', $today);

        $query = $qb->getQuery();
        $result = $query->getSingleScalarResult();

        return $result;
    }


    public function getProspectAvnEquipe()
    {
        $today = new \DateTime();
        $today->setTime(0, 0, 0);

        $qb = $this->manager->createQueryBuilder();
        $qb->select('COUNT(p)')
            ->from('App\Entity\Prospect', 'p')
            ->where('p.motifSaise = 3')
            ->andWhere('p.team = 4')
            ->andWhere('p.comrcl IS NOT NULL')
            ->andWhere('p.creatAt >= :startOfDay')
            ->setParameter('startOfDay', $today);

        $query = $qb->getQuery();
        $result = $query->getSingleScalarResult();

        return $result;
    }


    public function getProspectAncienEquipe()
    {
        $today = new \DateTime();
        $today->setTime(0, 0, 0);

        $qb = $this->manager->createQueryBuilder();
        $qb->select('COUNT(p)')
            ->from('App\Entity\Prospect', 'p')
            ->where('p.motifSaise = 4')
            ->andWhere('p.team = 4')
            ->andWhere('p.comrcl IS NOT NULL')
            ->andWhere('p.creatAt >= :startOfDay')
            ->setParameter('startOfDay', $today);

        $query = $qb->getQuery();
        $result = $query->getSingleScalarResult();

        return $result;
    }

    public function getProspectAutrEquipe()
    {
        $today = new \DateTime();
        $today->setTime(0, 0, 0);

        $qb = $this->manager->createQueryBuilder();
        $qb->select('COUNT(p)')
            ->from('App\Entity\Prospect', 'p')
            ->where('p.motifSaise is NULL')
            ->andWhere('p.team = 4')
            ->andWhere('p.comrcl IS NOT NULL')
            ->andWhere('p.creatAt >= :startOfDay')
            ->setParameter('startOfDay', $today);

        $query = $qb->getQuery();
        $result = $query->getSingleScalarResult();

        return $result;
    }


    public function getProspectSiteEquipe()
    {
        $today = new \DateTime();
        $today->setTime(0, 0, 0);

        $qb = $this->manager->createQueryBuilder();
        $qb->select('COUNT(p)')
            ->from('App\Entity\Prospect', 'p')
            ->where('p.motifSaise is NULL')
            ->andWhere('p.team = 4')
            ->andWhere("p.source = 'Propre site'")
            ->andWhere('p.comrcl IS NOT NULL')
            ->andWhere('p.creatAt >= :startOfDay')
            ->setParameter('startOfDay', $today);

        $query = $qb->getQuery();
        $result = $query->getSingleScalarResult();

        return $result;
    }




    public function getProspectRevendeurEquipe()
    {
        $today = new \DateTime();
        $today->setTime(0, 0, 0);

        $qb = $this->manager->createQueryBuilder();
        $qb->select('COUNT(p)')
            ->from('App\Entity\Prospect', 'p')
            ->where('p.motifSaise is NULL')
            ->andWhere('p.team = 4')
            ->andWhere("p.source = 'Revendeur'")
            ->andWhere('p.comrcl IS NOT NULL')
            ->andWhere('p.creatAt >= :startOfDay')
            ->setParameter('startOfDay', $today);

        $query = $qb->getQuery();
        $result = $query->getSingleScalarResult();

        return $result;
    }

    // public function getBestAds(){
    //     return $this->manager->createQuery(
    //         'SELECT AVG(c.rating) as note, a.title, a.id, u.firstName, u.lastName, u.picture
    //         FROM App\Entity\Comment c
    //         JOIN c.ad a
    //         JOIN a.author u
    //         GROUP BY a
    //         ORDER BY note DESC'
    //     )
    //     ->setMaxResults(5)
    //     ->getResult();
    // }
    // public function getWorstAds(){
    //     return $this->manager->createQuery(
    //         'SELECT AVG(c.rating) as note, a.title, a.id, u.firstName, u.lastName, u.picture
    //         FROM App\Entity\Comment c
    //         JOIN c.ad a
    //         JOIN a.author u
    //         GROUP BY a
    //         ORDER BY note ASC'
    //     )
    //     ->setMaxResults(5)
    //     ->getResult();
    // }
}
