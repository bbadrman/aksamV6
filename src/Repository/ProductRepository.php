<?php

namespace App\Repository;


use App\Entity\Product;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Product>
 *
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**  
     * @return void
     */
    public function add(Product $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**  
     * @return void
     */
    public function remove(Product $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findAllProductByAscNameQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('c')
            ->orderBy('c.name', 'ASC');
    }



    public function findProductByMonth(int $year, int $month): array
    {
        $startDate = new \DateTime("$year-$month-01");
        $endDate = (clone $startDate)->add(new \DateInterval('P1M'));

        $qb = $this->createQueryBuilder('r')
            ->join('r.prospects', 'p')


            ->andWhere('p.creatAt >= :start_date')
            ->andWhere('p.creatAt < :end_date')
            // ->andWhere('p.source = :source')
            // ->setParameter('source', $source)
            // ->andWhere('p.typeProspect = :typeProspect')
            // ->setParameter('typeProspect', $typeProspect)

            ->setParameter('start_date', $startDate)
            ->setParameter('end_date', $endDate)

            ->getQuery();

        return $qb->getResult();
    }

    // public function findByTeamOrderedByAscName(Team $team): array
    // {
    //     return $this->createQueryBuilder('c')
    //         ->andWhere('c.team = :team')
    //         ->setParameter('team', $team)
    //         ->orderBy('c.name', 'ASC')
    //         ->getQuery()
    //         ->getResult();
    // }

    // public function findAllOrderedByAscNameQueryBuilder(): QueryBuilder
    // {
    //     return $this->createQueryBuilder('c')
    //         ->orderBy('c.name', 'ASC');
    // }
    //    /**
    //     * @return Product[] Returns an array of Product objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Product
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
