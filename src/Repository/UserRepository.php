<?php

namespace App\Repository;

use App\Entity\Team;
use App\Entity\User;
use App\Search\SearchUser;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Security\Core\Security;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    /**
     * @var PaginatorInterface
     */

    public function __construct(ManagerRegistry $registry, private PaginatorInterface $paginator, private Security $security)
    {
        parent::__construct($registry, User::class);
    }


    public function add(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**  
     * @return void
     */
    public function remove(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time. 
     * @return void
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);

        $this->add($user, true);
    }


    public function findOnlineUsers()
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT u
            FROM App\Entity\User u
            WHERE u.lastLogin >= :cutoffTime'
        )->setParameter('cutoffTime', new \DateTime('-3500 minutes'));

        return $query->getResult();
    }

    /**
     * Find list a user by a search form
     * @param SearchUser $search
     * @return PaginationInterface
     */
    public function findSearchUser(SearchUser $search): PaginationInterface
    {
        $query = $this
            ->createQueryBuilder('u')
            ->select('u, t, f, p')
            // joiner les tables en relation ManyToOne avec team
            ->leftJoin('u.teams', 't')
            // joiner les tables en relation manytomany avec fonction
            ->leftJoin('u.fonctions', 'f')
            //relation manytomany avec product apartir team
            ->leftJoin('u.products', 'p')
            ->orderBy('u.id', 'asc');

        if (!empty($search->q)) {
            $query = $query
                ->Where('u.firstname LIKE :q')
                ->orWhere('u.username LIKE :q')
                ->orWhere('u.lastname LIKE :q')
                ->orWhere('u.remuneration LIKE :q')
                ->orWhere('u.embuchAt LIKE :q')
                // join les tables              
                ->orWhere('t.name LIKE :q')
                ->orWhere('p.name LIKE :q')
                ->orWhere('f.name LIKE :q')

                ->orWhere('u.status LIKE :q')
                ->orderBy('u.id', 'desc')
                ->setParameter('q', "%{$search->q}%");
        }
        if (isset($search->gender)) {
            $query = $query
                ->andWhere('u.gender = :gender')
                ->setParameter('gender', $search->gender);
        }
        if (isset($search->status)) {
            $query = $query
                ->andWhere('u.status = :status')
                ->setParameter('status', $search->status);
        }
        return $this->paginator->paginate(
            $query,
            $search->page,
            8

        );
    }

    public function findComrclByteamOrderedByAscName(User $user): array
    {
        $teams = $user->getTeams();
        if ($teams->isEmpty()) {
            return [];
        }

        return $this->createQueryBuilder('u')
            ->innerJoin('u.teams', 't') // Jointure entre les utilisateurs et les équipes
            ->andWhere('t IN (:teams)') // Condition pour filtrer les utilisateurs par les équipes
            ->setParameter('teams', $teams->toArray()) // Convertir la collection en tableau pour le paramètre
            ->orderBy('u.username', 'ASC')
            ->getQuery()
            ->getResult();
    }



    public function findClientByteamChef(User $user): array
    {
        $teams = $user->getTeams();

        if ($teams->isEmpty()) {
            return [];
        }
        return $this->createQueryBuilder('u')
            ->where('u.team IN (:teams)')
            ->setParameter('teams', $teams)
            ->orderBy('u.username', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByComrclMonth(int $year, int $month): array
    {
        $startDate = new \DateTime("$year-$month-01");
        $endDate = (clone $startDate)->add(new \DateInterval('P1M'));

        $qb = $this->createQueryBuilder('t')
            ->leftJoin('t.prospects', 'p')
            ->andWhere('p.creatAt >= :start_date')
            ->andWhere('p.creatAt < :end_date')
            ->setParameter('start_date', $startDate)
            ->setParameter('end_date', $endDate)
            ->getQuery();

        return $qb->getResult();
    }

    // selectionner les user activer
    public function findActifeCorcl()
    {


        // joiner les tables en relation ManyToOne avec team
        return $this->createQueryBuilder('u')
            ->andWhere('u.status = :val')
            ->setParameter('val', 1)
            ->getQuery()
            ->getResult();
    }

    // public function createQueryForGestionUsers($id): QueryBuilder
    // {
    //     return $this->createQueryBuilder('u')
    //         ->where('u.roles LIKE :role')
    //         // ->andWhere('u.afect = :val OR u.id = :currentUserId')
    //         // ->setParameter('val', $id)
    //         ->setParameter('role', '%ROLE_STAND%')
    //         // ->setParameter('currentUserId', $this->security->getUser())
    //         ->orderBy('u.username', 'ASC');
    // }
}
