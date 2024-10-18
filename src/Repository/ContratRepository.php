<?php

namespace App\Repository;

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
