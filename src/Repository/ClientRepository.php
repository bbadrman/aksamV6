<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Client;
use App\Search\SearchClient;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Security\Core\Security;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Client>
 *
 * @method Client|null find($id, $lockMode = null, $lockVersion = null)
 * @method Client|null findOneBy(array $criteria, array $orderBy = null)
 * @method Client[]    findAll()
 * @method Client[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClientRepository extends ServiceEntityRepository
{

    private $entityManager;
    private $paginator;
    private $security;


    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager, PaginatorInterface $paginator, Security $security)
    {
        parent::__construct($registry, Client::class);

        $this->entityManager = $entityManager;
        $this->paginator = $paginator;
        $this->security = $security;
    }



    public function add(Client $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Client $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


    /**
     * Find list a client by a all search form
     * @param SearchClient $search
     * @return PaginationInterface
     */
    public function findClientAdmin(SearchClient $search): PaginationInterface
    {

        $query = $this->createQueryBuilder('c')
            ->orderBy('c.id', 'DESC')
            ->getQuery()
            ->getResult();

        if ((!empty($search->f))) {
            $query = $query
                ->andWhere('c.firstname LIKE :f')


                ->setParameter('f', "%{$search->f}%");
        }

        if (!empty($search->l)) {
            $query = $query
                ->andWhere('c.lastname LIKE :l')
                ->setParameter('l', "%{$search->l}%");
        }

        if (!empty($search->g)) {
            $query = $query
                ->andWhere('c.email LIKE :g')
                ->setParameter('g', "%{$search->g}%");
        }

        if (!empty($search->a)) {
            $query = $query
                ->orWhere('c.adress LIKE :a')
                ->setParameter('a', "%{$search->a}%");
        }
        if (!empty($search->t)) {
            $query = $query
                ->orWhere('c.phone LIKE :t')
                ->setParameter('t', "%{$search->t}%");
        }
        return $this->paginator->paginate(
            $query,
            $search->page,
            10
        );
    }



    /**
     * Find list a client by a all search form
     * @param SearchClient $search
     * @return PaginationInterface
     */
    public function findClientChef(SearchClient $search, User $user): PaginationInterface
    {

        $team = $user->getTeams();

        $query = $this->createQueryBuilder('c')

            ->andwhere('c.team = :team')
            ->setParameter('team', $team)
            ->orderBy('c.id', 'DESC');

        if ((!empty($search->f))) {
            $query = $query
                ->andWhere('c.firstname LIKE :f')


                ->setParameter('f', "%{$search->f}%");
        }

        if (!empty($search->l)) {
            $query = $query
                ->andWhere('c.lastname LIKE :l')
                ->setParameter('l', "%{$search->l}%");
        }

        if (!empty($search->g)) {
            $query = $query
                ->andWhere('c.email LIKE :g')
                ->setParameter('g', "%{$search->g}%");
        }

        if (!empty($search->a)) {
            $query = $query
                ->orWhere('c.adress LIKE :a')
                ->setParameter('a', "%{$search->a}%");
        }
        if (!empty($search->t)) {
            $query = $query
                ->orWhere('c.phone LIKE :t')
                ->setParameter('t', "%{$search->t}%");
        }
        return $this->paginator->paginate(
            $query,
            $search->page,
            10
        );
    }

    /**
     * Find list a client by a all search form
     * @param SearchClient $search
     * @return PaginationInterface
     */
    public function findClientCmrcl(SearchClient $search, $id): PaginationInterface
    {


        $query = $this->createQueryBuilder('c')


            ->andWhere('c.cmrl = :val')
            ->setParameter('val', $id)
            ->orderBy('c.id', 'DESC');

        if ((!empty($search->f))) {
            $query = $query
                ->andWhere('c.firstname LIKE :f')


                ->setParameter('f', "%{$search->f}%");
        }

        if (!empty($search->l)) {
            $query = $query
                ->andWhere('c.lastname LIKE :l')
                ->setParameter('l', "%{$search->l}%");
        }

        if (!empty($search->g)) {
            $query = $query
                ->andWhere('c.email LIKE :g')
                ->setParameter('g', "%{$search->g}%");
        }

        if (!empty($search->a)) {
            $query = $query
                ->orWhere('c.adress LIKE :a')
                ->setParameter('a', "%{$search->a}%");
        }
        if (!empty($search->t)) {
            $query = $query
                ->orWhere('c.phone LIKE :t')
                ->setParameter('t', "%{$search->t}%");
        }
        return $this->paginator->paginate(
            $query,
            $search->page,
            10
        );
    }

    //    /**
    //     * @return Client[] Returns an array of Client objects
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

    //    public function findOneBySomeField($value): ?Client
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
