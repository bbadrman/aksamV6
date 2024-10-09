<?php

namespace App\Repository;

use App\Entity\RelanceSav;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RelanceSav>
 *
 * @method RelanceSav|null find($id, $lockMode = null, $lockVersion = null)
 * @method RelanceSav|null findOneBy(array $criteria, array $orderBy = null)
 * @method RelanceSav[]    findAll()
 * @method RelanceSav[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RelanceSavRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RelanceSav::class);
    }

//    /**
//     * @return RelanceSav[] Returns an array of RelanceSav objects
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

//    public function findOneBySomeField($value): ?RelanceSav
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
