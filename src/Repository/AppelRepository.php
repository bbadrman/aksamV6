<?php

namespace App\Repository;

use App\Entity\Appel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Appel>
 *
 * @method Appel|null find($id, $lockMode = null, $lockVersion = null)
 * @method Appel|null findOneBy(array $criteria, array $orderBy = null)
 * @method Appel[]    findAll()
 * @method Appel[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AppelRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Appel::class);
    }
    public function findByUniqueProperties(string $fromNumber, string $toNumber, \DateTime $startTime): ?Appel
    {
        return $this->findOneBy([
            'fromNumber' => $fromNumber,
            'toNumber' => $toNumber,
            'startTime' => $startTime,
        ]);
    }

    // Dans AppelRepository.php
    public function findAllOrderedByStartTime()
    {
        return $this->createQueryBuilder('a')
            ->orderBy('a.startTime', 'DESC')
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return Appel[] Returns an array of Appel objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('a.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Appel
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
