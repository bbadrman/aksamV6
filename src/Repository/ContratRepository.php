<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Contrat;
use App\Search\SearchContrat;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Contrat>
 *
 * @method Contrat|null find($id, $lockMode = null, $lockVersion = null)
 * @method Contrat|null findOneBy(array $criteria, array $orderBy = null)
 * @method Contrat[]    findAll()
 * @method Contrat[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContratRepository extends ServiceEntityRepository
{
    public function __construct(private ManagerRegistry $registry,   private PaginatorInterface $paginator)
    {
        parent::__construct($registry, Contrat::class);
    }


    /**
     * Find a list of contrat using a search form
     * @param SearchContrat $search
     * @return PaginationInterface
     */
    public function findByContartValid(SearchContrat $search): PaginationInterface
    {
        $queryBuilder = $this->createQueryBuilder('c')
            ->where('c.status = 2 OR c.status IS NULL')
            ->orderBy('c.id', 'DESC');

        if (!empty($search->f)) {
            $queryBuilder
                ->andWhere('c.nom LIKE :f')
                ->setParameter('f', "%{$search->f}%");
        }
        if (!empty($search->l)) {
            $queryBuilder
                ->andWhere('c.prenom LIKE :l')
                ->setParameter('l', "%{$search->l}%");
        }

        if (!empty($search->r)) {
            $queryBuilder
                ->andWhere('c.raisonSociale LIKE :r')
                ->setParameter('r', "%{$search->r}%");
        }
        if (!empty($search->e)) {
            $queryBuilder
                ->andWhere('c.etat LIKE :e')
                ->setParameter('e', "%{$search->e}%");
        }

        $query = $queryBuilder->getQuery();

        return $this->paginator->paginate(
            $query,
            $search->page,
            10
        );
    }
    /**
     * Find a list of contrat using a search form
     * @param SearchContrat $search
     * @return PaginationInterface
     */
    public function findByContartValideur(SearchContrat $search): PaginationInterface
    {
        $queryBuilder = $this->createQueryBuilder('c')
            ->where('c.status = 2 OR c.status IS NULL')
            ->orderBy('c.id', 'DESC');

        if (!empty($search->f)) {
            $queryBuilder
                ->andWhere('c.nom LIKE :f')
                ->setParameter('f', "%{$search->f}%");
        }
        if (!empty($search->l)) {
            $queryBuilder
                ->andWhere('c.prenom LIKE :l')
                ->setParameter('l', "%{$search->l}%");
        }

        if (!empty($search->r)) {
            $queryBuilder
                ->andWhere('c.raisonSociale LIKE :r')
                ->setParameter('r', "%{$search->r}%");
        }
        if (!empty($search->e)) {
            $queryBuilder
                ->andWhere('c.etat LIKE :e')
                ->setParameter('e', "%{$search->e}%");
        }

        $query = $queryBuilder->getQuery();

        return $this->paginator->paginate(
            $query,
            $search->page,
            10
        );
    }

    public function countContractsForCurrentMonth(): int
    {
        $startOfMonth = new \DateTime('first day of this month 00:00:00');
        $endOfMonth = new \DateTime('last day of this month 23:59:59');

        // dump($startOfMonth, $endOfMonth);
        return $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->where('c.dateSouscrpt BETWEEN :start AND :end')
            ->setParameter('start', $startOfMonth)
            ->setParameter('end', $endOfMonth)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Calcule la somme totale des frais
     */
    // public function getTotalFrais(): float
    // {
    //     return (float) $this->createQueryBuilder('c')
    //         ->select('SUM(c.frais)')
    //         ->getQuery()
    //         ->getSingleScalarResult();
    // }

    /**
     * Calcule les contrats par comrcl
     */
    // public function countContratsByComrcl(): array
    // {
    //     return $this->createQueryBuilder('c')
    //         ->select('u.id AS userId, u.username AS username, COUNT(c.id) AS contratCount')
    //         ->join('c.comrcl', 'u')
    //         ->groupBy('u.id')
    //         ->getQuery()
    //         ->getResult();
    // }

    /**
     * Calcule les frais par comrcl
     */
    // public function countContratsAndTotalFraisByComrcl(): array
    // {
    //     return $this->createQueryBuilder('c')
    //         ->select('u.id AS userId, u.username AS username, COUNT(c.id) AS contratCount, SUM(c.frais) AS totalFrais')
    //         ->join('c.comrcl', 'u')
    //         ->groupBy('u.id')
    //         ->getQuery()
    //         ->getResult();
    // }

    // public function countContratsAndTotalFraisByComrclForThisMonth(): array
    // {
    //     $currentMonth = new \DateTime('first day of this month');

    //     return $this->createQueryBuilder('c')
    //         ->select('u.id AS userId, u.username AS username, COUNT(c.id) AS contratCount, SUM(c.frais) AS totalFrais')
    //         ->join('c.comrcl', 'u')
    //         ->where('c.dateSouscrpt >= :startOfMonth')
    //         ->andWhere('c.dateSouscrpt < :endOfMonth')
    //         ->setParameter('startOfMonth', $currentMonth->format('Y-m-01'))
    //         ->setParameter('endOfMonth', $currentMonth->modify('first day of next month')->format('Y-m-01'))
    //         ->groupBy('u.id')
    //         ->getQuery()
    //         ->getResult();
    // }

    // public function getTotalContratsAndFraisForThisMonth(): array
    // {
    //     $currentMonth = new \DateTime('first day of this month');

    //     $result = $this->createQueryBuilder('c')
    //         ->select('COUNT(c.id) AS totalContrats, SUM(c.frais) AS totalFrais')
    //         ->where('c.dateSouscrpt >= :startOfMonth')
    //         ->andWhere('c.dateSouscrpt < :endOfMonth')
    //         ->setParameter('startOfMonth', $currentMonth->format('Y-m-01'))
    //         ->setParameter('endOfMonth', $currentMonth->modify('first day of next month')->format('Y-m-01'))
    //         ->getQuery()
    //         ->getSingleResult();

    //     return $result;
    // }


    // les donnes des contrats trimistrielle:
    // public function countContratsAndTotalFraisByComrclForLastThreeMonths(): array
    // {
    //     $threeMonthsAgo = (new \DateTime('first day of this month'))->modify('-2 months');
    //     $endOfCurrentMonth = (new \DateTime('first day of next month'));

    //     return $this->createQueryBuilder('c')
    //         ->select('u.id AS userId, u.username AS username, COUNT(c.id) AS contratCount, SUM(c.frais) AS totalFrais')
    //         ->join('c.comrcl', 'u')
    //         ->where('c.dateSouscrpt >= :startOfThreeMonths')
    //         ->andWhere('c.dateSouscrpt < :endOfCurrentMonth')
    //         ->setParameter('startOfThreeMonths', $threeMonthsAgo->format('Y-m-01'))
    //         ->setParameter('endOfCurrentMonth', $endOfCurrentMonth->format('Y-m-01'))
    //         ->groupBy('u.id')
    //         ->getQuery()
    //         ->getResult();
    // }





    // public function getTotalFraisForLastThreeMonths(): array
    // {
    //     $threeMonthsAgo = (new \DateTime('first day of this month'))->modify('-2 months');
    //     $endOfCurrentMonth = (new \DateTime('first day of next month'));

    //     $result = $this->createQueryBuilder('c')
    //         ->select('COUNT(c.id) AS totalContrats, SUM(c.frais) AS totalFrais')
    //         ->where('c.dateSouscrpt >= :startOfThreeMonths')
    //         ->andWhere('c.dateSouscrpt < :endOfCurrentMonth')
    //         ->setParameter('startOfThreeMonths', $threeMonthsAgo->format('Y-m-01'))
    //         ->setParameter('endOfCurrentMonth', $endOfCurrentMonth->format('Y-m-01'))
    //         ->getQuery()
    //         ->getSingleResult();

    //     return $result;
    // }

    // contrat by calendire :
    public function findByDateInterval(?\DateTime $startDate, ?\DateTime $endDate)
    {
        $qb = $this->createQueryBuilder('c');

        if ($startDate) {
            $qb->andWhere('c.dateSouscrpt >= :startDate')
                ->setParameter('startDate', $startDate->format('Y-m-d'));
        }

        if ($endDate) {
            $qb->andWhere('c.dateSouscrpt <= :endDate')
                ->setParameter('endDate', $endDate->format('Y-m-d'));
        }

        return $qb->getQuery()->getResult();
    }

    public function countContratsByComrclForInterval(\DateTimeInterface $startDate, \DateTimeInterface $endDate): array
    {
        return $this->createQueryBuilder('c')
            ->select('u.username AS commercial, COUNT(c.id) AS contratCount, SUM(c.frais) AS totalFrais, SUM(c.firstReglement) AS totalFirstReglement')
            ->join('c.comrcl', 'u') // Utilisez 'comrcl' pour la relation avec User
            ->where('c.dateSouscrpt BETWEEN :startDate AND :endDate')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->groupBy('u.id')
            ->getQuery()
            ->getResult();
    }


    public function getTotalContratsAndFraisForInterval(?\DateTime $startDate, ?\DateTime $endDate)
    {
        $qb = $this->createQueryBuilder('c')
            ->select('COUNT(c.id) as totalContrats, SUM(c.frais) as totalFrais');

        if ($startDate) {
            $qb->andWhere('c.dateSouscrpt >= :startDate')
                ->setParameter('startDate', $startDate->format('Y-m-d'));
        }

        if ($endDate) {
            $qb->andWhere('c.dateSouscrpt <= :endDate')
                ->setParameter('endDate', $endDate->format('Y-m-d'));
        }

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * Récupère le total des frais pour un intervalle de dates donné
     *
     * @param \DateTimeInterface $startDate La date de début de l'intervalle
     * @param \DateTimeInterface $endDate   La date de fin de l'intervalle
     * @return float|null Le total des frais ou null si aucune donnée
     */
    public function getTotalFraisForInterval(\DateTimeInterface $startDate, \DateTimeInterface $endDate): ?float
    {
        $qb = $this->createQueryBuilder('c')
            ->select('SUM(c.frais) as totalFrais')
            ->where('c.dateSouscrpt BETWEEN :startDate AND :endDate')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate);

        return $qb->getQuery()->getSingleScalarResult() ?? 0;
    }

    /**
     * Récupère le total des frais pour un intervalle de dates donné
     *
     * @param \DateTimeInterface $startDate La date de début de l'intervalle
     * @param \DateTimeInterface $endDate   La date de fin de l'intervalle
     * @return float|null Le total des frais ou null si aucune donnée
     */
    public function getTotalFirstReglmForInterval(\DateTimeInterface $startDate, \DateTimeInterface $endDate): ?float
    {
        $qb = $this->createQueryBuilder('c')
            ->select('SUM(c.firstReglement) as totalFirstReglm')
            ->where('c.dateSouscrpt BETWEEN :startDate AND :endDate')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate);

        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * Compte le nombre de contrats par commercial
     *
     * @return array
     */
    public function countContratsByComrclCaldr(): array
    {
        return $this->createQueryBuilder('c')
            ->select('com.contrats AS commercialName, COUNT(c.id) AS contratCount')
            ->join('c.comrcl', 'com') // Supposons que l'entité "Contrat" a une relation "commercial"
            ->groupBy('com.id')
            ->orderBy('contratCount', 'DESC')
            ->getQuery()
            ->getResult();
    }
    // /**
    //  * Find a list of contrat using a search form
    //  * @param SearchContrat $search
    //  * @return PaginationInterface
    //  */
    // public function findByContartValidChef(SearchContrat $search, User $user): PaginationInterface
    // {
    //     $teams = $user->getTeams();

    //     if ($teams->isEmpty()) {
    //         return [];
    //     }


    //     $queryBuilder = $this->createQueryBuilder('c')
    //         ->select('c, f, cl ')
    //         ->leftJoin('c.comrcl', 'f')  // Jointure avec l'utilisateur commercial
    //         ->leftJoin('c.client', 'cl')  // Jointure avec le client
    //         ->where('c.status = 2 OR c.status IS NULL')
    //         ->andWhere('cl.team IN (:teams)')  // Filtrer par équipe via le client
    //         ->setParameter('teams', $teams)
    //         ->orderBy('c.id', 'DESC');

    //     if (!empty($search->f)) {
    //         $queryBuilder
    //             ->andWhere('c.nom LIKE :f')
    //             ->setParameter('f', "%{$search->f}%");
    //     }
    //     if (!empty($search->l)) {
    //         $queryBuilder
    //             ->andWhere('c.prenom LIKE :l')
    //             ->setParameter('l', "%{$search->l}%");
    //     }

    //     if (!empty($search->r)) {
    //         $queryBuilder
    //             ->andWhere('c.raisonSociale LIKE :r')
    //             ->setParameter('r', "%{$search->r}%");
    //     }
    //     if (!empty($search->e)) {
    //         $queryBuilder
    //             ->andWhere('c.etat LIKE :e')
    //             ->setParameter('e', "%{$search->e}%");
    //     }

    //     $query = $queryBuilder->getQuery();

    //     return $this->paginator->paginate(
    //         $query,
    //         $search->page,
    //         10
    //     );
    // }

    /**
     * Find a list of contrat using a search form
     * @param SearchContrat $search
     * @return PaginationInterface
     */
    public function findByContartValidComrcl(SearchContrat $search, $id): PaginationInterface
    {


        $queryBuilder = $this->createQueryBuilder('c')
            ->select('c, f ')
            ->where('c.status = 2 OR c.status IS NULL')
            ->leftJoin('c.comrcl', 'f')

            ->andWhere('c.comrcl = :val')
            ->setParameter('val', $id)
            ->orderBy('c.id', 'DESC');

        if (!empty($search->f)) {
            $queryBuilder
                ->andWhere('c.nom LIKE :f')
                ->setParameter('f', "%{$search->f}%");
        }
        if (!empty($search->l)) {
            $queryBuilder
                ->andWhere('c.prenom LIKE :l')
                ->setParameter('l', "%{$search->l}%");
        }

        if (!empty($search->r)) {
            $queryBuilder
                ->andWhere('c.raisonSociale LIKE :r')
                ->setParameter('r', "%{$search->r}%");
        }
        if (!empty($search->e)) {
            $queryBuilder
                ->andWhere('c.etat LIKE :e')
                ->setParameter('e', "%{$search->e}%");
        }

        $query = $queryBuilder->getQuery();

        return $this->paginator->paginate(
            $query,
            $search->page,
            10
        );
    }
    // /**
    //  * @return Contrat[] Returns an array of Contrat objects
    //  */
    // public function findByContartValid($value): array
    // {
    //     return $this->createQueryBuilder('c')
    //         ->andWhere('c.exampleField = :val')
    //         ->setParameter('val', $value)
    //         ->orderBy('c.id', 'ASC')
    //         ->setMaxResults(10)
    //         ->getQuery()
    //         ->getResult()
    //     ;
    // }

    //    public function findOneBySomeField($value): ?Contrat
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
