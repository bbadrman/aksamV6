<?php

namespace App\Repository;

use DateTime;
use App\Entity\Team;
use App\Search\SearchTeam;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Team>
 *
 * @method Team|null find($id, $lockMode = null, $lockVersion = null)
 * @method Team|null findOneBy(array $criteria, array $orderBy = null)
 * @method Team[]    findAll()
 * @method Team[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TeamRepository extends ServiceEntityRepository
{
    /**
     * @var PaginatorInterface
     */
    private $paginator;
    public function __construct(ManagerRegistry $registry, PaginatorInterface $paginator)
    {
        parent::__construct($registry, Team::class);
        $this->paginator = $paginator;
    }

    public function add(Team $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Team $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    // public function findAllOrderedByAscNameQueryBuilder(): QueryBuilder
    // {
    //     return $this->createQueryBuilder('c')
    //         ->orderBy('c.name', 'ASC');
    // }

    /**
     * @param SearchTeam $search
     * @return PaginationInterface
     */

    public function findSearch(SearchTeam $search): PaginationInterface
    {
        $query = $this
            ->createQueryBuilder('g')
            ->select('g');

        if (!empty($search->q)) {
            $query = $query
                ->andWhere('g.name LIKE :q')
                ->orWhere('g.description LIKE :q')
                //  ->orWhere('g.team.users LIKE :q')
                //  ->orWhere('g.team.products LIKE :q')
                ->setParameter('q', "%{$search->q}%");
        }
        return $this->paginator->paginate(
            $query,
            $search->page,
            9

        );
    }

    public function findByMonth(int $year, int $month): array
    {
        $startDate = new \DateTime("$year-$month-01");
        $endDate = (clone $startDate)->add(new \DateInterval('P1M'));

        $qb = $this->createQueryBuilder('t')
            ->join('t.prospects', 'p')
            ->andWhere('p.creatAt >= :start_date')
            ->andWhere('p.creatAt < :end_date')
            ->setParameter('start_date', $startDate)
            ->setParameter('end_date', $endDate)
            ->getQuery();

        return $qb->getResult();
    }


    public function findByMonthAndTeam(int $year, int $month, int $teamId): array
    {
        $startDate = new \DateTime("$year-$month-01");
        $endDate = (clone $startDate)->add(new \DateInterval('P1M'));

        $qb = $this->createQueryBuilder('t')
            ->join('t.prospects', 'p')
            ->andWhere('p.creatAt >= :start_date')
            ->andWhere('p.creatAt < :end_date')
            ->andWhere('t.id = :team_id') // Ajoutez cette condition pour filtrer par équipe
            ->setParameter('start_date', $startDate)
            ->setParameter('end_date', $endDate)
            ->setParameter('team_id', $teamId) // Assurez-vous de passer l'ID de l'équipe ici
            ->getQuery();

        return $qb->getResult();
    }



    public function findAllTeamByAscNameQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('c')
            ->orderBy('c.name', 'ASC');
    }

    /**
     * @return Team[] Returns an array of Prospect objects 
     */
    public function findByTeamConect($id): array
    {

        return $this->createQueryBuilder('t')
            ->join('t.users', 'u')
            ->andWhere('u.id = :val')
            ->setParameter('val', $id)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    public function findAllOrderByAscNameQuiryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('c')->orderBy('c.name', 'ASC');
    }
    //    /**
    //     * @return Team[] Returns an array of Team objects
    //     */ 
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.Field = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('t.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Team
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    // public function getProducts($value){
    //  return $this->createQueryBuilder('t')
    //             ->select('p.name')
    //             ->innerJoin('t.products', 'p')
    //             ->Where('t.id = :value')
    //             ->setParameter('value', $value)
    //             ->getQuery()
    //             ->getResult();


    // }
}
