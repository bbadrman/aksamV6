<?php

namespace App\Repository;

use App\Entity\Transaction;
use App\Search\SearchTransaction;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Transaction>
 *
 * @method Transaction|null find($id, $lockMode = null, $lockVersion = null)
 * @method Transaction|null findOneBy(array $criteria, array $orderBy = null)
 * @method Transaction[]    findAll()
 * @method Transaction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TransactionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry,  private PaginatorInterface $paginator, private EntityManagerInterface $manager)
    {
        parent::__construct($registry, Transaction::class);
    }



    /**
     * Find list a user by a search form
     * @param SearchTransaction $search
     * @return PaginationInterface
     */
    public function findSearchTransaction(SearchTransaction $search): PaginationInterface
    {
        $query = $this
            ->createQueryBuilder('t')
            ->orderBy('t.id', 'desc');

        if (!empty($search->q)) {
            $query = $query
                ->Where('t.commande LIKE :q')

                ->setParameter('q', "%{$search->q}%");
        }
        if (!empty($search->d) && $search->d instanceof \DateTime) {
            $query = $query
                ->andWhere('t.datePaiement >= :d')
                ->setParameter('d', $search->d);
        }

        if (!empty($search->dd) && $search->dd instanceof \DateTime) {
            $search->dd->setTime(23, 59, 59);
            $query = $query
                ->andWhere('t.datePaiement <= :dd')
                ->setParameter('dd', $search->dd);
        }

        if (isset($search->motif)) {
            $query = $query
                ->andWhere('t.motif = :motif')
                ->setParameter('motif', $search->motif);
        }
        if (isset($search->moyen)) {
            $query = $query
                ->andWhere('t.moyen = :moyen')
                ->setParameter('moyen', $search->moyen);
        }
        return $this->paginator->paginate(
            $query,
            $search->page,
            10

        );
    }

    //    /**
    //     * @return Transaction[] Returns an array of Transaction objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('t.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Transaction
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
