<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Prospect;
use App\Search\SearchProspect;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Security\Core\Security;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Prospect>
 *
 * @method Prospect|null find($id, $lockMode = null, $lockVersion = null)
 * @method Prospect|null findOneBy(array $criteria, array $orderBy = null)
 * @method Prospect[]    findAll()
 * @method Prospect[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProspectRepository extends ServiceEntityRepository
{

    private $entityManager;
    private $paginator;
    private $security;


    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager, PaginatorInterface $paginator, Security $security)
    {
        parent::__construct($registry, Prospect::class);

        $this->entityManager = $entityManager;
        $this->paginator = $paginator;
        $this->security = $security;
    }

    public function add(Prospect $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Prospect $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


    /**
     * Find list a prospect by a all search form
     * @param SearchProspect $search
     * @return PaginationInterface
     */
    public function findSearch(SearchProspect $search, User $user): PaginationInterface
    {


        $team = $user->getTeams();

        $query = $this
            ->createQueryBuilder('u')
            ->select('u, t, f, h')

            ->leftJoin('u.team', 't')

            ->leftJoin('u.comrcl', 'f')

            ->leftJoin('u.relanceds', 'h')

            ->orderBy('u.id', 'DESC');



        if ((!empty($search->q))) {
            $query = $query
                ->andWhere('u.name LIKE :q')

                ->orderBy('u.id', 'desc')
                ->setParameter('q', "%{$search->q}%");
        }

        if (!empty($search->m)) {
            $query = $query
                ->andWhere('u.lastname LIKE :m')
                ->setParameter('m', "%{$search->m}%");
        }
        if (!empty($search->r)) {
            $query = $query
                ->andWhere('f.username LIKE :r')
                ->setParameter('r', "%{$search->r}%");
        }
        if (!empty($search->g)) {
            $query = $query
                ->andWhere('u.email LIKE :g')
                ->setParameter('g', "%{$search->g}%");
        }
        if (!empty($search->team)) {
            $query = $query
                ->andWhere('t.name LIKE :team')
                ->setParameter('team', "%{$search->team}%");
        }
        if (!empty($search->l)) {
            $query = $query
                ->orWhere('u.phone LIKE :l')
                ->orWhere('u.gsm LIKE :l')
                ->setParameter('l', "%{$search->l}%");
        }
        if (!empty($search->c)) {
            $query = $query
                ->andWhere('u.city LIKE :c')
                ->setParameter('c', "%{$search->c}%");
        }

        if (!empty($search->d)) {

            $query = $query
                ->andWhere('u.creatAt >= :d')
                ->setParameter('d', $search->d->format('Y-m-d'));
        }
        if (!empty($search->dd)) {
            $search->dd->modify('+23 hours 59 minutes 59 seconds');
            $query = $query
                // ->andWhere('u.creatAt LIKE :dd')
                ->andWhere('u.creatAt <= :dd')
                ->setParameter('dd', $search->dd->format('Y-m-d H:i:s'));
        }


        if (!empty($search->s)) {
            $query = $query
                ->andWhere('u.raisonSociale LIKE :s')
                ->setParameter('s', "%{$search->s}%");
        }
        if (!empty($search->source)) {
            $query = $query
                ->andWhere('u.source = :source')
                ->setParameter('source', $search->source);
        }

        //sherche par relance
        if (!empty($search->dr)) {

            $query = $query

                ->andWhere('h.relacedAt >= :dr')
                ->setParameter('dr', $search->dr->format('Y-m-d'));
        }
        if (!empty($search->ddr)) {
            $search->ddr->modify('+23 hours 59 minutes 59 seconds');
            $query = $query

                ->andWhere('h.relacedAt <= :ddr')
                ->setParameter('ddr', $search->ddr->format('Y-m-d H:i:s'));
        }




        if (!empty($search->motifRelanced)) {
            $query = $query

                ->andWhere('h.motifRelanced = :motifRelanced')
                ->setParameter('motifRelanced', $search->motifRelanced);
        }



        return $this->paginator->paginate(
            $query,
            $search->page,

            10
        );
    }

    /**
     * Find list a user by a search form
     * @param SearchProspect $search
     * @return PaginationInterface
     */
    public function findSearchChef(SearchProspect $search, User $user): PaginationInterface
    {
        $team = $user->getTeams();


        $query = $this
            ->createQueryBuilder('u')
            ->select('u')
            ->orderBy('u.id', 'DESC')
            ->where('u = 0');


        return $this->paginator->paginate(
            $query,
            $search->page,
            4

        );
    }

    /**
     * Find list a prospect Relanced
     * @param SearchProspect $search
     * @return PaginationInterface
     */
    public function findRelanced(SearchProspect $search): PaginationInterface
    {


        $today = new \DateTime();
        $today->setTime(0, 0, 0);

        $endOfDay = clone $today;
        $endOfDay->setTime(23, 59, 59);

        // get selement les prospects qui n'as pas encors affectter a un user
        $query = $this->createQueryBuilder('p')
            ->select('p, t, f, r')
            ->leftJoin('p.relanceds', 'r')
            ->andWhere('r.relacedAt BETWEEN :startOfDay AND :endOfDay')
            ->setParameter('startOfDay', $today)
            ->setParameter('endOfDay', $endOfDay)

            // joiner les tables en relation ManyToOne avec team
            ->leftJoin('p.team', 't')


            // joiner les tables en relation manytomany avec fonction
            ->leftJoin('p.comrcl', 'f')

            ->orderBy('p.id', 'DESC');



        if ((!empty($search->q))) {
            $query = $query
                ->andWhere('p.name LIKE :q')

                ->orderBy('p.id', 'desc')
                ->setParameter('q', "%{$search->q}%");
        }

        if (!empty($search->m)) {
            $query = $query
                ->andWhere('p.lastname LIKE :m')
                ->setParameter('m', "%{$search->m}%");
        }
        if (!empty($search->r)) {
            $query = $query
                ->andWhere('f.username LIKE :r')
                ->setParameter('r', "%{$search->r}%");
        }
        if (!empty($search->g)) {
            $query = $query
                ->andWhere('p.email LIKE :g')
                ->setParameter('g', "%{$search->g}%");
        }
        if (!empty($search->team)) {
            $query = $query
                ->andWhere('t.name LIKE :team')
                ->setParameter('team', "%{$search->team}%");
        }
        if (!empty($search->l)) {
            $query = $query
                ->andWhere('p.phone LIKE :l')
                ->orWhere('p.gsm LIKE :l')
                ->setParameter('l', "%{$search->l}%");
        }
        if (!empty($search->c)) {
            $query = $query
                ->andWhere('p.city LIKE :c')
                ->setParameter('c', "%{$search->c}%");
        }

        if (!empty($search->d)) {

            $query = $query
                ->andWhere('p.creatAt >= :d')
                ->setParameter('d', $search->d->format('Y-m-d'));
        }
        if (!empty($search->dd)) {
            $search->dd->modify('+23 hours 59 minutes 59 seconds');
            $query = $query
                // ->andWhere('u.creatAt LIKE :dd')
                ->andWhere('p.creatAt <= :dd')
                ->setParameter('dd', $search->dd->format('Y-m-d H:i:s'));
        }


        if (!empty($search->s)) {
            $query = $query
                ->andWhere('p.raisonSociale LIKE :s')
                ->setParameter('s', "%{$search->s}%");
        }
        if (!empty($search->source)) {
            $query = $query
                ->andWhere('p.source = :source')
                ->setParameter('source', $search->source);
        }
        return $this->paginator->paginate(
            $query,
            $search->page,
            10

        );
    }


    /**
     * Find list a prospect no traite (qui sont pas de motirelance et dejat affecter au team et cmrcl)
     * @param SearchProspect $search
     * @return PaginationInterface
     */
    public function findNonTraiter(SearchProspect $search): PaginationInterface
    {
        // $now = new \DateTime();
        // $yesterday = clone $now;
        // $yesterday->modify('-24 hours');

        $query = $this->createQueryBuilder('p')
            ->select('p, t, f, r')
            ->leftJoin('p.relanceds', 'r')
            ->andWhere('r.prospect IS NULL') // Aucune relation avec relanced
            ->andWhere('p.team IS NOT NULL')  // Affecté à une équipe
            ->andWhere('p.comrcl IS NOT NULL') // Affecté à un commercial
            ->leftJoin('p.team', 't')
            ->leftJoin('p.comrcl', 'f')
            ->orderBy('p.id', 'DESC');
        if ((!empty($search->q))) {
            $query = $query
                ->andWhere('p.name LIKE :q')

                ->orderBy('p.id', 'desc')
                ->setParameter('q', "%{$search->q}%");
        }

        if (!empty($search->m)) {
            $query = $query
                ->andWhere('p.lastname LIKE :m')
                ->setParameter('m', "%{$search->m}%");
        }
        if (!empty($search->r)) {
            $query = $query
                ->andWhere('f.username LIKE :r')
                ->setParameter('r', "%{$search->r}%");
        }
        if (!empty($search->g)) {
            $query = $query
                ->andWhere('p.email LIKE :g')
                ->setParameter('g', "%{$search->g}%");
        }
        if (!empty($search->team)) {
            $query = $query
                ->andWhere('t.name LIKE :team')
                ->setParameter('team', "%{$search->team}%");
        }
        if (!empty($search->l)) {
            $query = $query
                ->andWhere('p.phone LIKE :l')
                ->orWhere('p.gsm LIKE :l')
                ->setParameter('l', "%{$search->l}%");
        }
        if (!empty($search->c)) {
            $query = $query
                ->andWhere('p.city LIKE :c')
                ->setParameter('c', "%{$search->c}%");
        }

        if (!empty($search->d)) {

            $query = $query
                ->andWhere('p.creatAt >= :d')
                ->setParameter('d', $search->d->format('Y-m-d'));
        }
        if (!empty($search->dd)) {
            $search->dd->modify('+23 hours 59 minutes 59 seconds');
            $query = $query
                // ->andWhere('u.creatAt LIKE :dd')
                ->andWhere('p.creatAt <= :dd')
                ->setParameter('dd', $search->dd->format('Y-m-d H:i:s'));
        }


        if (!empty($search->s)) {
            $query = $query
                ->andWhere('p.raisonSociale LIKE :s')
                ->setParameter('s', "%{$search->s}%");
        }
        if (!empty($search->source)) {
            $query = $query
                ->andWhere('p.source = :source')
                ->setParameter('source', $search->source);
        }

        // Vos autres conditions de recherche restent inchangées.

        return $this->paginator->paginate(
            $query,
            $search->page,
            10
        );
    }


    /**
     * Find list a prospect Unjoinable
     * @return Prospect[] 
     * @param SearchProspect $search
     * @return PaginationInterface 
     */
    public function findUnjoing(SearchProspect $search): PaginationInterface
    {

        $query = $this->createQueryBuilder('p')
            ->select('p, t, f')
            ->leftJoin('p.relanceds', 'r')
            ->Where('r.motifRelanced = 2')

            // joiner les tables en relation ManyToOne avec team
            ->leftJoin('p.team', 't')

            // joiner les tables en relation manytomany avec fonction
            ->leftJoin('p.comrcl', 'f');

        if ((!empty($search->q))) {
            $query = $query
                ->andWhere('p.name LIKE :q')

                ->orderBy('p.id', 'desc')
                ->setParameter('q', "%{$search->q}%");
        }

        if (!empty($search->m)) {
            $query = $query
                ->andWhere('p.lastname LIKE :m')
                ->setParameter('m', "%{$search->m}%");
        }
        if (!empty($search->r)) {
            $query = $query
                ->andWhere('f.username LIKE :r')
                ->setParameter('r', "%{$search->r}%");
        }
        if (!empty($search->g)) {
            $query = $query
                ->andWhere('p.email LIKE :g')
                ->setParameter('g', "%{$search->g}%");
        }
        if (!empty($search->team)) {
            $query = $query
                ->andWhere('t.name LIKE :team')
                ->setParameter('team', "%{$search->team}%");
        }
        if (!empty($search->l)) {
            $query = $query
                ->andWhere('p.phone LIKE :l')
                ->orWhere('p.gsm LIKE :l')
                ->setParameter('l', "%{$search->l}%");
        }
        if (!empty($search->c)) {
            $query = $query
                ->andWhere('p.city LIKE :c')
                ->setParameter('c', "%{$search->c}%");
        }

        if (!empty($search->d)) {

            $query = $query
                ->andWhere('p.creatAt >= :d')
                ->setParameter('d', $search->d->format('Y-m-d'));
        }
        if (!empty($search->dd)) {
            $search->dd->modify('+23 hours 59 minutes 59 seconds');
            $query = $query
                // ->andWhere('u.creatAt LIKE :dd')
                ->andWhere('p.creatAt <= :dd')
                ->setParameter('dd', $search->dd->format('Y-m-d H:i:s'));
        }


        if (!empty($search->s)) {
            $query = $query
                ->andWhere('p.raisonSociale LIKE :s')
                ->setParameter('s', "%{$search->s}%");
        }
        if (!empty($search->source)) {
            $query = $query
                ->andWhere('p.source = :source')
                ->setParameter('source', $search->source);
        }

        $query->orderBy('p.id', 'DESC');
        return $this->paginator->paginate(
            $query,
            $search->page,
            10

        );
    }

    // lister les prospects du comcrl
    /**
     * @return Prospect[] Returns an array of Prospect objects 
     * @param SearchProspect $search
     * @return PaginationInterface 
     */
    public function findByUserConect(SearchProspect $search, $id): PaginationInterface
    {


        $query = $this->createQueryBuilder('p')
            ->andWhere('p.comrcl = :val')
            ->setParameter('val', $id)
            ->orderBy('p.id', 'DESC');

        if (isset($search->d)) {

            $query = $query
                ->andWhere('p.creatAt >= :d')
                ->setParameter('d', $search->d->format('Y-m-d'));
        }
        if (isset($search->dd)) {
            $search->dd->modify('+23 hours 59 minutes 59 seconds');
            $query = $query
                // ->andWhere('u.creatAt LIKE :dd')
                ->andWhere('p.creatAt <= :dd')
                ->setParameter('dd', $search->dd->format('Y-m-d H:i:s'));
        }

        if (isset($search->m)) {
            $query = $query
                ->andWhere('p.lastname LIKE :m')
                ->setParameter('m', "%{$search->m}%");
        }
        if (!empty($search->q)) {
            $query = $query

                ->Where('p.name LIKE :q')
                ->setParameter('q', "%{$search->q}%");
        }
        if (isset($search->m)) {
            $query = $query
                ->andWhere('p.lastname LIKE :m')
                ->setParameter('m', "%{$search->m}%");
        }
        if (isset($search->source)) {
            $query = $query
                ->andWhere('p.source = :source')
                ->setParameter('source', $search->source);
        }
        if (isset($search->g)) {
            $query = $query
                ->andWhere('p.email LIKE :g')
                ->setParameter('g', "%{$search->g}%");
        }
        if (isset($search->c)) {
            $query = $query
                ->andWhere('p.city LIKE :c')
                ->setParameter('c', "%{$search->c}%");
        }
        if (isset($search->l)) {
            $query = $query
                ->andWhere('p.phone LIKE :l')
                ->setParameter('l', "%{$search->l}%");
        }
        if (isset($search->s)) {
            $query = $query
                ->andWhere('p.raisonSociale LIKE :s')
                ->setParameter('s', "%{$search->s}%");
        }


        return $this->paginator->paginate(
            $query,
            $search->page,
            10

        );
    }



    /**
     * @return Prospect[] Returns an array of Prospect objects
     * @param SearchProspect $search
     * @return PaginationInterface
     */
    public function findByUserAffecter(): array
    {
        // get selement les prospects qui sont affectter a un user
        return $this->createQueryBuilder('p')
            ->andWhere("p.comrcl != ''")
            ->orWhere("p.team is NOT NULL")
            ->orderBy('p.id', 'DESC')
            ->getQuery()
            ->getResult();
    }


    /**
     * @return Prospect[] Returns an array of Prospect objects
     * @param SearchProspect $search
     * @return PaginationInterface
     */
    public function findByUserAffecterChef(User $user, SearchProspect $search): PaginationInterface
    {
        $team = $user->getTeams();

        $query = $this
            ->createQueryBuilder('u')
            ->select('u, t, f')
            ->where('u.team = :team')
            ->setParameter('team', $team)
            ->andWhere("u.comrcl is NOT NULL")
            ->andWhere("u.team is NOT NULL")
            ->orderBy('u.id', 'DESC')
            // joiner les tables en relation ManyToOne avec team
            ->leftJoin('u.team', 't')
            // joiner les tables en relation manytomany avec fonction
            ->leftJoin('u.comrcl', 'f');
        //relation manytomany avec product apartir team
        // ->leftJoin('u.products', 'p');


        if (!empty($search->q)) {
            $query = $query

                ->Where('u.name LIKE :q')
                ->orWhere('u.lastname LIKE :q')
                ->orWhere('u.email LIKE :q')
                ->orWhere('u.city LIKE :q')
                ->orWhere('u.codePost LIKE :q')
                ->orWhere('u.gender LIKE :q')


                // join les tables              
                ->orWhere('t.name LIKE :q')
                ->orWhere('f.username LIKE :q')
                // ->orWhere('f.prospects LIKE :q')
                ->orWhere('u.phone LIKE :q')
                ->orWhere('u.gsm LIKE :q')
                ->orderBy('u.id', 'DESC')
                ->setParameter('q', "%{$search->q}%");
        }



        if (isset($search->source)) {
            $query = $query
                ->andWhere('u.source = :source')
                ->setParameter('source', $search->source);
        }

        return $this->paginator->paginate(
            $query,
            $search->page,
            10

        );
    }

    // afficher les prospects qui n ont pas du team et cmrcl
    /**
     * @return Prospect[] Returns an array of Prospect objects
     * 
     * @param SearchProspect $search
     * @return PaginationInterface 
     */
    public function findByUserPaAffecter(SearchProspect $search): PaginationInterface
    {
        $today = new \DateTime();
        $today->setTime(0, 0, 0);


        // get selement les prospects qui n'as pas encors affectter a un user
        $query = $this->createQueryBuilder('p')
            ->select('p, t, f')
            ->andWhere('p.creatAt >= :startOfDay')
            ->setParameter('startOfDay', $today)
            ->orderBy('p.id', 'DESC')



            // joiner les tables en relation ManyToOne avec team
            ->leftJoin('p.team', 't')

            // joiner les tables en relation manytomany avec fonction
            ->leftJoin('p.comrcl', 'f');


        if ((!empty($search->q))) {
            $query = $query
                ->andWhere('p.name LIKE :q')

                ->orderBy('p.id', 'desc')
                ->setParameter('q', "%{$search->q}%");
        }

        if (!empty($search->m)) {
            $query = $query
                ->andWhere('p.lastname LIKE :m')
                ->setParameter('m', "%{$search->m}%");
        }
        if (!empty($search->r)) {
            $query = $query
                ->andWhere('f.username LIKE :r')
                ->setParameter('r', "%{$search->r}%");
        }
        if (!empty($search->g)) {
            $query = $query
                ->andWhere('p.email LIKE :g')
                ->setParameter('g', "%{$search->g}%");
        }
        if (!empty($search->team)) {
            $query = $query
                ->andWhere('t.name LIKE :team')
                ->setParameter('team', "%{$search->team}%");
        }
        if (!empty($search->l)) {
            $query = $query
                ->andWhere('p.phone LIKE :l')
                ->orWhere('p.gsm LIKE :l')
                ->setParameter('l', "%{$search->l}%");
        }
        if (!empty($search->c)) {
            $query = $query
                ->andWhere('p.city LIKE :c')
                ->setParameter('c', "%{$search->c}%");
        }

        if (!empty($search->d)) {

            $query = $query
                ->andWhere('p.creatAt >= :d')
                ->setParameter('d', $search->d->format('Y-m-d'));
        }
        if (!empty($search->dd)) {
            $search->dd->modify('+23 hours 59 minutes 59 seconds');
            $query = $query
                // ->andWhere('u.creatAt LIKE :dd')
                ->andWhere('p.creatAt <= :dd')
                ->setParameter('dd', $search->dd->format('Y-m-d H:i:s'));
        }


        if (!empty($search->s)) {
            $query = $query
                ->andWhere('p.raisonSociale LIKE :s')
                ->setParameter('s', "%{$search->s}%");
        }
        if (!empty($search->source)) {
            $query = $query
                ->andWhere('p.source = :source')
                ->setParameter('source', $search->source);
        }

        return $this->paginator->paginate(
            $query,
            $search->page,
            10

        );
    }
    // afficher les prospects qui n ont pas du team et cmrcl
    /**
     * @return Prospect[] Returns an array of Prospect objects
     * 
     * @param SearchProspect $search
     * @return PaginationInterface 
     */
    public function findByUserAffecterCmrcl(SearchProspect $search, $id): PaginationInterface
    {
        $today = new \DateTime();
        $today->setTime(0, 0, 0);
        // get selement les prospects qui n'as pas encors affectter a un user
        $query = $this->createQueryBuilder('p')
            ->andWhere('p.comrcl = :val')
            ->setParameter('val', $id)
            ->andWhere('p.creatAt >= :startOfDay')
            ->setParameter('startOfDay', $today)
            ->orderBy('p.id', 'DESC');

        if ((!empty($search->q))) {
            $query = $query
                ->andWhere('p.name LIKE :q')

                ->setParameter('q', "%{$search->q}%");
        }
        if (isset($search->m)) {
            $query = $query
                ->andWhere('p.lastname LIKE :m')
                ->setParameter('m', "%{$search->m}%");
        }
        return $this->paginator->paginate(
            $query,
            $search->page,
            10

        );
    }



    /**
     * @return Prospect[] Returns an array of Prospect objects 
     * @param SearchProspect $search
     * @return PaginationInterface
     */
    public function findAllSearch(SearchProspect $search): PaginationInterface
    {
        // get selement les prospects qui n'as pas encors affectter a un user
        $query = $this
            ->createQueryBuilder('p')
            ->select('p, f, t')

            // joiner les tables en relation ManyToOne avec team et user(comrcl)
            ->leftJoin('p.team', 't')
            ->leftJoin('p.comrcl', 'f');

        if (isset($search->d)) {

            $query = $query
                ->andWhere('p.creatAt >= :d')
                ->setParameter('d', $search->d->format('Y-m-d'));
        }
        if (isset($search->dd)) {
            $search->dd->modify('+23 hours 59 minutes 59 seconds');
            $query = $query
                // ->andWhere('u.creatAt LIKE :dd')
                ->andWhere('p.creatAt <= :dd')
                ->setParameter('dd', $search->dd->format('Y-m-d H:i:s'));
        }

        if (!empty($search->q)) {
            $query = $query

                ->Where('p.name LIKE :q')
                ->setParameter('q', "%{$search->q}%");
        }
        if (isset($search->m)) {
            $query = $query
                ->andWhere('p.lastname LIKE :m')
                ->setParameter('m', "%{$search->m}%");
        }
        if (isset($search->source)) {
            $query = $query
                ->andWhere('p.source = :source')
                ->setParameter('source', $search->source);
        }
        if (isset($search->g)) {
            $query = $query
                ->andWhere('p.email LIKE :g')
                ->setParameter('g', "%{$search->g}%");
        }
        if (isset($search->c)) {
            $query = $query
                ->andWhere('p.city LIKE :c')
                ->setParameter('c', "%{$search->c}%");
        }
        if (isset($search->l)) {
            $query = $query
                ->andWhere('p.phone LIKE :l')
                ->setParameter('l', "%{$search->l}%");
        }
        if (isset($search->s)) {
            $query = $query
                ->andWhere('p.raisonSociale LIKE :s')
                ->setParameter('s', "%{$search->s}%");
        }
        if (isset($search->r)) {
            $query = $query
                ->orWhere('f.username LIKE :r')
                ->setParameter('r', "%{$search->r}%");
        }
        if (isset($search->team)) {
            $query = $query
                ->andWhere('t.name LIKE :team')
                ->setParameter('team', "%{$search->team}%");
        }
        $query->orderBy('p.id', 'DESC');
        return $this->paginator->paginate(
            $query,
            $search->page,
            10

        );
    }

    // afecher seulement les prospects qui partient de panier du chef findByUserChefEquipe

    /**
     * @return Prospect[] Returns an array of Prospect objects 
     * @param SearchProspect $search
     * @return PaginationInterface
     */
    public function findAllChefSearch(SearchProspect $search, User $user): PaginationInterface
    {
        // get selement les prospects qui n'as pas encors affectter a un user
        $team = $user->getTeams();
        $query = $this
            ->createQueryBuilder('p')

            // joiner les tables en relation ManyToOne avec team
            ->where('p.team = :team')
            // ->andWhere("p.comrcl is NULL")
            ->setParameter('team', $team)
            ->orderBy('p.id', 'DESC');


        if ((!empty($search->q))) {
            $query = $query
                ->andWhere('p.name LIKE :q')

                ->setParameter('q', "%{$search->q}%");
        }

        if (isset($search->d)) {

            $query = $query
                ->andWhere('p.creatAt >= :d')
                ->setParameter('d', $search->d->format('Y-m-d'));
        }
        if (isset($search->dd)) {
            $search->dd->modify('+23 hours 59 minutes 59 seconds');
            $query = $query
                // ->andWhere('u.creatAt LIKE :dd')
                ->andWhere('p.creatAt <= :dd')
                ->setParameter('dd', $search->dd->format('Y-m-d H:i:s'));
        }


        if (isset($search->m)) {
            $query = $query
                ->andWhere('p.lastname LIKE :m')
                ->setParameter('m', "%{$search->m}%");
        }
        if (isset($search->source)) {
            $query = $query
                ->andWhere('p.source = :source')
                ->setParameter('source', $search->source);
        }
        if (isset($search->g)) {
            $query = $query
                ->andWhere('p.email LIKE :g')
                ->setParameter('g', "%{$search->g}%");
        }
        if (isset($search->c)) {
            $query = $query
                ->andWhere('p.city LIKE :c')
                ->setParameter('c', "%{$search->c}%");
        }
        if (isset($search->l)) {
            $query = $query
                ->andWhere('p.phone LIKE :l')
                ->setParameter('l', "%{$search->l}%");
        }
        if (isset($search->s)) {
            $query = $query
                ->andWhere('p.raisonSociale LIKE :s')
                ->setParameter('s', "%{$search->s}%");
        }

        return $this->paginator->paginate(
            $query,
            $search->page,
            10

        );
    }

    // afficher seulement les prospects qui apartient au chef d equipe et ne sont pas affiter au cmrcl
    /**
     * @return Prospect[] Returns an array of Prospect objects
     * @param SearchProspect $search
     * @return PaginationInterface 
     */
    public function findByUserChefEquipe(SearchProspect $search, User $user): PaginationInterface
    {
        $team = $user->getTeams();

        $query = $this->createQueryBuilder('p')
            ->where('p.team = :team')
            ->andWhere("p.comrcl is NULL")
            ->orderBy('p.id', 'DESC')
            ->setParameter('team', $team);

        if ((!empty($search->q))) {
            $query = $query
                ->andWhere('p.name LIKE :q')

                ->setParameter('q', "%{$search->q}%");
        }
        if (isset($search->m)) {
            $query = $query
                ->andWhere('p.lastname LIKE :m')
                ->setParameter('m', "%{$search->m}%");
        }
        return $this->paginator->paginate(
            $query,
            $search->page,
            10

        );
    }

    // afficher seulement les relance du chef equipe
    /**
     * @return Prospect[] Returns an array of Prospect objects
     * @param SearchProspect $search
     * @return PaginationInterface 
     */
    public function findByRelanceChefEquipe(SearchProspect $search, User $user): PaginationInterface
    {
        $team = $user->getTeams();
        $query = $this->createQueryBuilder('p')
            ->where('p.team = :team')
            ->orderBy('p.id', 'DESC')
            ->setParameter('team', $team);

        if ((!empty($search->q))) {
            $query = $query
                ->andWhere('p.name LIKE :q')

                ->setParameter('q', "%{$search->q}%");
        }
        if (isset($search->m)) {
            $query = $query
                ->andWhere('p.lastname LIKE :m')
                ->setParameter('m', "%{$search->m}%");
        }
        return $this->paginator->paginate(
            $query,
            $search->page,
            10

        );
    }

    /**
     * @return Prospect[] Returns an array of Prospect objects  
     */
    public function findOneByChef(User $user): array
    {
        $team = $user->getTeams();

        $qb = $this->createQueryBuilder('p')
            ->where('p.team = :team')
            ->andWhere("p.comrcl is NOT NULL")
            ->orderBy('p.id', 'DESC')
            ->setParameter('team', $team);

        $prospects = $qb->getQuery()->getResult();

        return $prospects;




        // select * from prospect join user on prospect.comrcl_id = user.id where prospect.comrcl_id = 2;
    }
}
