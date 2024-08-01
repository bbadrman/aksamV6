<?php

namespace App\Repository;

use App\Entity\Cloture;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Cloture>
 *
 * @method Cloture|null find($id, $lockMode = null, $lockVersion = null)
 * @method Cloture|null findOneBy(array $criteria, array $orderBy = null)
 * @method Cloture[]    findAll()
 * @method Cloture[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClotureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cloture::class);
    }

//    /**
//     * @return Cloture[] Returns an array of Cloture objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Cloture
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
