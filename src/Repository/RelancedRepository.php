<?php

namespace App\Repository;

use App\Entity\Relanced;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Relanced>
 *
 * @method Relanced|null find($id, $lockMode = null, $lockVersion = null)
 * @method Relanced|null findOneBy(array $criteria, array $orderBy = null)
 * @method Relanced[]    findAll()
 * @method Relanced[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RelancedRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Relanced::class);
    }


    //    /**
    //     * @return Relanced[] Returns an array of Relanced objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Relanced
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
