<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Prospect;
use App\Search\SearchProspect;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\Walker\LimitWalker;
use Knp\Component\Pager\PaginatorInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Knp\Component\Pager\Pagination\PaginationInterface;
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


    public function __construct(ManagerRegistry $registry, private  PaginatorInterface $paginator, private EntityManagerInterface $manager)
    {
        parent::__construct($registry, Prospect::class);
    }

    /**  
     * @return void
     */
    public function add(Prospect $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**  
     * @return void
     */
    public function remove(Prospect $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }





    public function findByDateInterval(\DateTimeInterface $startDate, \DateTimeInterface $endDate): array
    {
        return $this->createQueryBuilder('p')


            ->where('p.creatAt BETWEEN :startDate AND :endDate')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->getQuery()
            ->getResult();
    }


    // $startDate = new \DateTime("$year-$month-01"); 
    // $endDate = (clone $startDate)->modify('last day of this month');  
    // return $this->createQueryBuilder('p') 
    //     ->where('p.creatAt BETWEEN :startDate AND :endDate') 
    //     ->setParameter('startDate', $startDate) 
    //     ->setParameter('endDate', $endDate) 
    //     ->getQuery() 
    //     ->getResult();

    // utilise pas  
    // public function findProspectsByMonth(int $year, int $month): array
    // {
    //     $startDate = new \DateTime("$year-$month-01");
    //     $endDate = (clone $startDate)->add(new \DateInterval('P1M'));

    //     $qb = $this->createQueryBuilder('p')
    //         ->andWhere('p.creatAt >= :start_date')
    //         ->andWhere('p.creatAt < :end_date')
    //         ->setParameter('start_date', $startDate)
    //         ->setParameter('end_date', $endDate)
    //         ->getQuery();

    //     return $qb->getResult();
    // }



    //modifie
    public function findByMonthWithTeamAndProduct(int $year, int $month): array
    {
        $startDate = new \DateTime("$year-$month-01");
        $endDate = (clone $startDate)->modify('last day of this month');

        return $this->createQueryBuilder('p')
            ->leftJoin('p.team', 't')
            ->leftJoin('p.product', 'prod')
            ->addSelect('t', 'prod')
            ->where('p.creatAt BETWEEN :startDate AND :endDate')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->orderBy('t.name', 'ASC')
            ->addOrderBy('prod.name', 'ASC')
            ->getQuery()
            ->getResult();
    }



    /**
     * Find list a prospect by a all search form
     * @param SearchProspect $search
     * @return PaginationInterface
     */
    public function findSearch(SearchProspect $search): PaginationInterface
    {

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

        if (!empty($search->d) && $search->d instanceof \DateTime) {
            $query = $query
                ->andWhere('u.creatAt >= :d')
                ->setParameter('d', $search->d);
        }

        if (!empty($search->dd) && $search->dd instanceof \DateTime) {
            $search->dd->setTime(23, 59, 59);
            $query = $query
                ->andWhere('u.creatAt <= :dd')
                ->setParameter('dd', $search->dd);
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

        if (!empty($search->dr) && $search->dr instanceof \DateTime) {
            $query = $query
                ->andWhere('h.relacedAt >= :dr')
                ->setParameter('dr', $search->dr->format('Y-m-d'));
        }

        if (!empty($search->ddr) && $search->ddr instanceof \DateTime) {
            $search->ddr->setTime(23, 59, 59); // Fix time to end of the day
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
     * affichier les prospect avenir pour admin
     * je doit modifie cet fn afin de disparer prospc quand an faire une action
     * dans ce cas il dispare demain
     * Find list a prospect Relanced
     * @param SearchProspect $search
     * @return PaginationInterface
     */
    public function findAvenir(SearchProspect $search): PaginationInterface
    {


        $tomorrow = new \DateTime('tomorrow');
        $tomorrow->setTime(0, 0, 0);
        $subQuery = $this->manager->createQueryBuilder()
            ->select('MAX(r1.relacedAt)')
            ->from('App\Entity\Relanced', 'r1')
            ->where('r1.prospect = p.id')
            ->getDQL();

        $query = $this->createQueryBuilder('p')

            ->select('p,  r')

            ->leftJoin('p.relanceds', 'r')
            ->andWhere('r.relacedAt > :tomorrow')
            ->setParameter('tomorrow', $tomorrow)

            //pour que n'affiche pas les motif not 7, 8, 9, 10 dans ce table
            // ->andWhere('NOT EXISTS (
            //     SELECT 1 FROM App\Entity\Relanced otherR
            //     WHERE otherR.prospect = p AND otherR.motifRelanced IN (7, 8, 9, 10)
            // )') ou bien desu

            ->andWhere('r.motifRelanced NOT IN (:motifs)')
            ->setParameter('motifs', [3, 7, 8, 9, 10])
            // joiner les tables en relation ManyToOne avec team
            //->leftJoin('p.team', 't')


            // joiner les tables en relation manytomany avec fonction
            //->leftJoin('p.comrcl', 'f')
            ->addSelect('(' . $subQuery . ') AS HIDDEN lastRelanceDate')
            ->orderBy('lastRelanceDate', 'ASC');
        // ->orderBy('p.id', 'DESC');



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

        if (!empty($search->d) && $search->d instanceof \DateTime) {
            $query = $query
                ->andWhere('p.creatAt >= :d')
                ->setParameter('d', $search->d);
        }

        if (!empty($search->dd) && $search->dd instanceof \DateTime) {
            $search->dd->setTime(23, 59, 59);
            $query = $query
                ->andWhere('p.creatAt <= :dd')
                ->setParameter('dd', $search->dd);
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
     * Find list a prospect Relanced no traités pour admin--modifie-- 
     * @param SearchProspect $search
     * @return PaginationInterface
     */
    public function findRelancesNonTraitees(SearchProspect $search): PaginationInterface
    {

        $yesterday = (new \DateTime('yesterday'))->setTime(23, 59, 59);

        $dayBeforeYesterday = (clone $yesterday)->modify('-1 year');
        $subQuery = $this->manager->createQueryBuilder()
            ->select('MAX(r1.relacedAt)')
            ->from('App\Entity\Relanced', 'r1')
            ->where('r1.prospect = p.id')
            ->getDQL();
        $query = $this->createQueryBuilder('p')

            ->select('p, t, f, r')
            ->leftJoin('p.team', 't')
            ->leftJoin('p.comrcl', 'f')
            ->leftJoin('p.relanceds', 'r')
            //->leftJoin('p.relanceds', 'r', 'WITH', 'r.motifRelanced = 1')

            // ->leftJoin('p.relanceds', 'r') 

            // ->Where('(r.motifRelanced = 1)') // r.motifRelanced selement = 1
            ->andWhere('r.relacedAt > :dayBeforeYesterday  ')
            ->setParameter('dayBeforeYesterday', $dayBeforeYesterday)

            ->andWhere('p.comrcl is NOT NULL')
            ->andWhere('p.team is NOT NULL')
            ->andWhere('p.id NOT IN ( 
                SELECT pr.id FROM App\Entity\Prospect pr
                JOIN pr.relanceds rel
                WHERE rel.relacedAt > :endOfYesterday
            )')

            ->setParameter('endOfYesterday', $yesterday)
            ->andWhere('r.motifRelanced NOT IN (:motifs)')
            ->setParameter('motifs', [3, 7, 8, 9, 10])
            ->addSelect('(' . $subQuery . ') AS HIDDEN lastRelanceDate')
            ->orderBy('lastRelanceDate', 'ASC');



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

        if (!empty($search->d) && $search->d instanceof \DateTime) {
            $query = $query
                ->andWhere('p.creatAt >= :d')
                ->setParameter('d', $search->d);
        }

        if (!empty($search->dd) && $search->dd instanceof \DateTime) {
            $search->dd->setTime(23, 59, 59);
            $query = $query
                ->andWhere('p.creatAt <= :dd')
                ->setParameter('dd', $search->dd);
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
     * Find list a prospect Relanced no traités
     * @param SearchProspect $search
     * @return PaginationInterface
     */

    public function RelancesNonTraiteesChef(SearchProspect $search, User $user): PaginationInterface
    {
        $teams = $user->getTeams();

        if ($teams->isEmpty()) {
            return [];
        }

        $yesterday = new \DateTime('yesterday');
        $yesterday->setTime(23, 59, 59); // La fin de la journée d'hier

        $dayBeforeYesterday = (clone $yesterday)->modify('-1 year')->setTime(0, 0, 0); // Le début d'avant-hier


        $subQuery = $this->manager->createQueryBuilder()
            ->select('MAX(r1.relacedAt)')
            ->from('App\Entity\Relanced', 'r1')
            ->where('r1.prospect = p.id')
            ->getDQL();
        $query = $this->createQueryBuilder('p')

            ->select('p, t, f, r')
            ->leftJoin('p.relanceds', 'r')
            ->leftJoin('p.team', 't')
            ->leftJoin('p.comrcl', 'f')
            ->where('p.team IN (:teams)')
            ->setParameter('teams', $teams)

            //->andWhere('(r.motifRelanced IS NULL OR r.motifRelanced = 1)') 

            ->andWhere('r.relacedAt >= :dayBeforeYesterday AND r.relacedAt <= :yesterday')
            ->setParameter('dayBeforeYesterday', $dayBeforeYesterday)
            ->setParameter('yesterday', $yesterday)
            //->andWhere('p.comrcl is NOT NULL') 
            ->andWhere('r.motifRelanced NOT IN (:motifs)')
            ->setParameter('motifs', [3, 7, 8, 9, 10])
            ->andWhere('p.id NOT IN (
                SELECT pr.id FROM App\Entity\Prospect pr
                JOIN pr.relanceds rel
                WHERE rel.relacedAt > :endOfYesterday
            )')->setParameter('endOfYesterday', $yesterday)

            ->addSelect('(' . $subQuery . ') AS HIDDEN lastRelanceDate')
            ->orderBy('lastRelanceDate', 'ASC');


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

        if (!empty($search->d) && $search->d instanceof \DateTime) {
            $query = $query
                ->andWhere('p.creatAt >= :d')
                ->setParameter('d', $search->d);
        }

        if (!empty($search->dd) && $search->dd instanceof \DateTime) {
            $search->dd->setTime(23, 59, 59);
            $query = $query
                ->andWhere('p.creatAt <= :dd')
                ->setParameter('dd', $search->dd);
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


    public function RelancesNonTraiteesCmrcl(SearchProspect $search, $id): PaginationInterface
    {


        $yesterday = new \DateTime('yesterday');
        $yesterday->setTime(23, 59, 59); // La fin de la journée d'hier

        $dayBeforeYesterday = (clone $yesterday)->modify('-1 month')->setTime(0, 0, 0); // Le début d'avant-hier



        $subQuery = $this->manager->createQueryBuilder()
            ->select('MAX(r1.relacedAt)')
            ->from('App\Entity\Relanced', 'r1')
            ->where('r1.prospect = p.id')
            ->getDQL();
        $query = $this->createQueryBuilder('p')

            ->select('p, t, f, r')
            ->leftJoin('p.relanceds', 'r')
            ->leftJoin('p.team', 't')
            ->leftJoin('p.comrcl', 'f')
            ->andWhere('p.comrcl = :val')
            ->setParameter('val', $id)
            //->andWhere('(r.motifRelanced IS NULL OR r.motifRelanced = 1)')

            ->andWhere('r.relacedAt >= :dayBeforeYesterday AND r.relacedAt <= :yesterday')
            ->setParameter('dayBeforeYesterday', $dayBeforeYesterday)
            ->setParameter('yesterday', $yesterday)
            //->andWhere('p.comrcl is NOT NULL')
            ->andWhere('r.motifRelanced NOT IN (:motifs)')
            ->setParameter('motifs', [3, 7, 8, 9, 10])

            ->andWhere('p.id NOT IN (
                SELECT pr.id FROM App\Entity\Prospect pr
                JOIN pr.relanceds rel
                WHERE rel.relacedAt > :endOfYesterday
            )')->setParameter('endOfYesterday', $yesterday)
            ->addSelect('(' . $subQuery . ') AS HIDDEN lastRelanceDate')
            ->orderBy('lastRelanceDate', 'ASC');

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

        if (!empty($search->d) && $search->d instanceof \DateTime) {
            $query = $query
                ->andWhere('p.creatAt >= :d')
                ->setParameter('d', $search->d);
        }

        if (!empty($search->dd) && $search->dd instanceof \DateTime) {
            $search->dd->setTime(23, 59, 59);
            $query = $query
                ->andWhere('p.creatAt <= :dd')
                ->setParameter('dd', $search->dd);
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
     * Find list a prospect Relanced
     * @param SearchProspect $search
     * @return PaginationInterface
     */
    public function findAvenirChef(SearchProspect $search, User $user): PaginationInterface
    {
        $teams = $user->getTeams();
        if ($teams->isEmpty()) {
            return [];
        }

        $tomorrow = new \DateTime('tomorrow');
        $tomorrow->setTime(0, 0, 0);
        $subQuery = $this->manager->createQueryBuilder()
            ->select('MAX(r1.relacedAt)')
            ->from('App\Entity\Relanced', 'r1')
            ->where('r1.prospect = p.id')
            ->getDQL();

        $query = $this->createQueryBuilder('p')

            ->select('p, t, f, r')
            ->where('p.team IN (:teams)')
            ->setParameter('teams', $teams)
            ->leftJoin('p.relanceds', 'r')
            ->andWhere('r.relacedAt >= :tomorrow')
            ->setParameter('tomorrow', $tomorrow)
            ->andWhere('r.motifRelanced NOT IN (:motifs)')
            ->setParameter('motifs', [3, 7, 8, 9, 10])



            // ->andWhere('NOT EXISTS (
            //     SELECT 1 FROM App\Entity\Relanced otherR
            //     WHERE otherR.prospect = p AND otherR.motifRelanced = 2
            // )')
            // joiner les tables en relation ManyToOne avec team
            ->leftJoin('p.team', 't')


            // joiner les tables en relation manytomany avec fonction
            ->leftJoin('p.comrcl', 'f')
            ->addSelect('(' . $subQuery . ') AS HIDDEN lastRelanceDate')
            ->orderBy('lastRelanceDate', 'ASC');
        //->orderBy('p.id', 'DESC');



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

        if (!empty($search->d) && $search->d instanceof \DateTime) {
            $query = $query
                ->andWhere('p.creatAt >= :d')
                ->setParameter('d', $search->d);
        }
        if (!empty($search->dd) && $search->dd instanceof \DateTime) {
            $search->dd->setTime(23, 59, 59);
            $query = $query
                ->andWhere('p.creatAt <= :dd')
                ->setParameter('dd', $search->dd);
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
     * Find list a prospect Relanced
     * @param SearchProspect $search
     * @return PaginationInterface
     */
    public function findAvenirCmrcl(SearchProspect $search, $id): PaginationInterface
    {


        $tomorrow = new \DateTime('tomorrow');
        $tomorrow->setTime(0, 0, 0);
        $subQuery = $this->manager->createQueryBuilder()
            ->select('MAX(r1.relacedAt)')
            ->from('App\Entity\Relanced', 'r1')
            ->where('r1.prospect = p.id')
            ->getDQL();

        $query = $this->createQueryBuilder('p')

            ->select('p, t, f, r')
            ->andWhere('p.comrcl = :val')
            ->setParameter('val', $id)
            ->leftJoin('p.relanceds', 'r')
            ->andWhere('r.relacedAt >= :tomorrow')
            ->setParameter('tomorrow', $tomorrow)
            ->andWhere('r.motifRelanced NOT IN (:motifs)')
            ->setParameter('motifs', [3, 7, 8, 9, 10])



            // ->andWhere('NOT EXISTS (
            //     SELECT 1 FROM App\Entity\Relanced otherR
            //     WHERE otherR.prospect = p AND otherR.motifRelanced != 1
            // )')

            // ->andWhere('NOT EXISTS (
            //     SELECT 1 FROM App\Entity\Relanced otherR
            //     WHERE otherR.prospect = p AND otherR.motifRelanced = 2
            // )')
            // joiner les tables en relation ManyToOne avec team
            ->leftJoin('p.team', 't')


            // joiner les tables en relation manytomany avec fonction
            ->leftJoin('p.comrcl', 'f')
            ->addSelect('(' . $subQuery . ') AS HIDDEN lastRelanceDate')
            ->orderBy('lastRelanceDate', 'ASC');
        //->orderBy('p.id', 'DESC');



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

        if (!empty($search->d) && $search->d instanceof \DateTime) {
            $query = $query
                ->andWhere('p.creatAt >= :d')
                ->setParameter('d', $search->d);
        }
        if (!empty($search->dd) && $search->dd instanceof \DateTime) {
            $search->dd->setTime(23, 59, 59);
            $query = $query
                ->andWhere('p.creatAt <= :dd')
                ->setParameter('dd', $search->dd);
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
     * Find list a prospect Relanced jour
     * @param SearchProspect $search
     * @return PaginationInterface
     */
    public function findRelanced(SearchProspect $search): PaginationInterface
    {


        $today = new \DateTime();
        $today->setTime(0, 0, 0);

        $endOfDay = clone $today;
        $endOfDay->setTime(23, 59, 59);
        $subQuery = $this->manager->createQueryBuilder()
            ->select('MAX(r1.relacedAt)')
            ->from('App\Entity\Relanced', 'r1')
            ->where('r1.prospect = p.id')
            ->getDQL();
        $query = $this->createQueryBuilder('p')
            ->select('p, r')
            ->leftJoin('p.relanceds', 'r')
            ->where('r.relacedAt BETWEEN :startOfDay AND :endOfDay')
            ->setParameter('startOfDay', $today)
            ->setParameter('endOfDay', $endOfDay)
            ->andWhere('r.motifRelanced NOT IN (:motifs)')
            ->setParameter('motifs', [3, 7, 8, 9, 10])
            ->addSelect('(' . $subQuery . ') AS HIDDEN lastRelanceDate')
            ->orderBy('lastRelanceDate', 'ASC');

        // ->addSelect("(SELECT FROM relanced ) AS HIDDEN ORD ")
        // ->orderBy('ORD', 'DESC');
        // joiner les tables en relation manytomany avec fonction
        // ->leftJoin('p.comrcl', 'f')
        // ->groupBy('p.id')
        // ->orderBy('r.relacedAt', 'DESC');

        //->orderBy('p.id', 'ASC');


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

        if (!empty($search->d) && $search->d instanceof \DateTime) {
            $query = $query
                ->andWhere('p.creatAt >= :d')
                ->setParameter('d', $search->d);
        }

        if (!empty($search->dd) && $search->dd instanceof \DateTime) {
            $search->dd->setTime(23, 59, 59);
            $query = $query
                ->andWhere('p.creatAt <= :dd')
                ->setParameter('dd', $search->dd);
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
     * Find list a prospect Relanced
     * @param SearchProspect $search
     * @return PaginationInterface
     */
    public function findRelancedChef(SearchProspect $search, User $user): PaginationInterface
    {
        $teams = $user->getTeams();
        if ($teams->isEmpty()) {
            return [];
        }

        $today = new \DateTime();
        $today->setTime(0, 0, 0);

        $endOfDay = clone $today;
        $endOfDay->setTime(23, 59, 59);
        $subQuery = $this->manager->createQueryBuilder()
            ->select('MAX(r1.relacedAt)')
            ->from('App\Entity\Relanced', 'r1')
            ->where('r1.prospect = p.id')
            ->getDQL();
        $query = $this->createQueryBuilder('p')
            ->select('p, f, r')
            ->where('p.team IN (:teams)')
            ->setParameter('teams', $teams)
            ->leftJoin('p.relanceds', 'r')
            ->andWhere('r.relacedAt BETWEEN :startOfDay AND :endOfDay')
            ->setParameter('startOfDay', $today)
            ->setParameter('endOfDay', $endOfDay)
            ->andWhere('r.motifRelanced NOT IN (:motifs)')
            ->setParameter('motifs', [3, 7, 8, 9, 10])


            // joiner les tables en relation manytomany avec fonction
            ->leftJoin('p.comrcl', 'f')
            ->addSelect('(' . $subQuery . ') AS HIDDEN lastRelanceDate')
            ->orderBy('lastRelanceDate', 'ASC');

        //->orderBy('p.id', 'DESC');



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

        if (!empty($search->d) && $search->d instanceof \DateTime) {
            $query = $query
                ->andWhere('p.creatAt >= :d')
                ->setParameter('d', $search->d);
        }

        if (!empty($search->dd) && $search->dd instanceof \DateTime) {
            $search->dd->setTime(23, 59, 59);
            $query = $query
                ->andWhere('p.creatAt <= :dd')
                ->setParameter('dd', $search->dd);
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
     * Find list a prospect Relanced
     * @param SearchProspect $search
     * @return PaginationInterface
     */
    public function findRelancedCmrcl(SearchProspect $search, $id): PaginationInterface
    {

        $today = new \DateTime();
        $today->setTime(0, 0, 0);

        $endOfDay = clone $today;
        $endOfDay->setTime(23, 59, 59);
        $subQuery = $this->manager->createQueryBuilder()
            ->select('MAX(r1.relacedAt)')
            ->from('App\Entity\Relanced', 'r1')
            ->where('r1.prospect = p.id')
            ->getDQL();
        $query = $this->createQueryBuilder('p')
            ->select('p, r')
            ->Where('p.comrcl = :val')
            ->setParameter('val', $id)
            ->leftJoin('p.relanceds', 'r')
            ->andWhere('r.relacedAt BETWEEN :startOfDay AND :endOfDay')
            ->setParameter('startOfDay', $today)
            ->setParameter('endOfDay', $endOfDay)
            //->andWhere('r.motifRelanced = 1') 
            ->andWhere('r.motifRelanced NOT IN (:motifs)')
            ->setParameter('motifs', [3, 7, 8, 9, 10])
            ->addSelect('(' . $subQuery . ') AS HIDDEN lastRelanceDate')
            ->orderBy('lastRelanceDate', 'ASC');

        // joiner les tables en relation manytomany avec fonction 
        //->orderBy('p.id', 'DESC');


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

        if (!empty($search->g)) {
            $query = $query
                ->andWhere('p.email LIKE :g')
                ->setParameter('g', "%{$search->g}%");
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

        if (!empty($search->d) && $search->d instanceof \DateTime) {
            $query = $query
                ->andWhere('p.creatAt >= :d')
                ->setParameter('d', $search->d);
        }

        if (!empty($search->dd) && $search->dd instanceof \DateTime) {
            $search->dd->setTime(23, 59, 59);
            $query = $query
                ->andWhere('p.creatAt <= :dd')
                ->setParameter('dd', $search->dd);
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
     * Find list a prospect no traite (qui sont pas de motifrelance et dejat affecter au team et cmrcl)
     * @param SearchProspect $search
     * @return PaginationInterface
     */
    public function findNonTraiter(SearchProspect $search): PaginationInterface
    {
        $now = new \DateTime();
        $yesterday = clone $now;
        $yesterday->modify('-24 hours');

        $query = $this->createQueryBuilder('p')
            ->select('p, t, f, r')
            ->leftJoin('p.relanceds', 'r')
            ->andWhere('r.prospect IS NULL') // Aucune relation avec relanced
            ->andWhere('p.team IS NOT NULL')  // Affecté à une équipe 

            // ->andWhere('p.comrcl IS NOT NULL')   
            ->andWhere('p.creatAt <= :yesterday')
            ->setParameter('yesterday', $yesterday)
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

        if (!empty($search->d) && $search->d instanceof \DateTime) {
            $query = $query
                ->andWhere('p.creatAt >= :d')
                ->setParameter('d', $search->d);
        }

        if (!empty($search->dd) && $search->dd instanceof \DateTime) {
            $search->dd->setTime(23, 59, 59);
            $query = $query
                ->andWhere('p.creatAt <= :dd')
                ->setParameter('dd', $search->dd);
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
     * Find list a prospect no traite (qui sont pas de motirelance et dejat affecter au team et cmrcl)
     * @param SearchProspect $search
     * @return PaginationInterface
     */
    public function findNonTraiterChef(SearchProspect $search, User $user): PaginationInterface
    {
        $now = new \DateTime();
        $yesterday = clone $now;
        $yesterday->modify('-24 hours');

        $teams = $user->getTeams();
        if ($teams->isEmpty()) {
            return [];
        }

        $query = $this->createQueryBuilder('p')
            ->select('p, f, r')
            ->where('p.team IN (:teams)')
            ->setParameter('teams', $teams)

            ->leftJoin('p.relanceds', 'r')
            ->andWhere('r.prospect IS NULL') // Aucune relation avec relanced
            ->andWhere('p.team IS NOT NULL')  // chef d'equipe affecté 
            //->andWhere('p.comrcl IS NOT NULL')
            ->andWhere('p.comrcl IS NULL OR p.comrcl = :val') // Filtrer les prospects no affectés et affect au chef aussi
            ->setParameter('val', $user)
            ->andWhere('p.creatAt <= :yesterday')
            ->setParameter('yesterday', $yesterday)
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

        if (!empty($search->d) && $search->d instanceof \DateTime) {
            $query = $query
                ->andWhere('p.creatAt >= :d')
                ->setParameter('d', $search->d);
        }

        if (!empty($search->dd) && $search->dd instanceof \DateTime) {
            $search->dd->setTime(23, 59, 59);
            $query = $query
                ->andWhere('p.creatAt <= :dd')
                ->setParameter('dd', $search->dd);
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
     * Find list a prospect no traite (qui sont pas de motirelance et dejat affecter au team et cmrcl)
     * @param SearchProspect $search
     * @return PaginationInterface
     */
    public function findNonTraiterCmrcl(SearchProspect $search, $id): PaginationInterface
    {
        $now = new \DateTime();
        $yesterday = clone $now;
        $yesterday->modify('-24 hours');

        $query = $this->createQueryBuilder('p')
            ->select('p,   r')
            ->andWhere('p.comrcl = :val')
            ->setParameter('val', $id)
            ->leftJoin('p.relanceds', 'r')
            ->andWhere('r.prospect IS NULL')
            ->andWhere('p.creatAt <= :yesterday')
            ->setParameter('yesterday', $yesterday)
            // pas encour passe un jeur de la date de history actionDate
            ->leftJoin('p.histories', 'h') // Jointure avec l'entité History
            ->andWhere('h.actionDate <= :endOfYesterday') // Filtre par date d'action de l'historique
            ->setParameter('endOfYesterday', $yesterday)



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

        if (!empty($search->g)) {
            $query = $query
                ->andWhere('p.email LIKE :g')
                ->setParameter('g', "%{$search->g}%");
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

        if (!empty($search->d) && $search->d instanceof \DateTime) {
            $query = $query
                ->andWhere('p.creatAt >= :d')
                ->setParameter('d', $search->d);
        }

        if (!empty($search->dd) && $search->dd instanceof \DateTime) {
            $search->dd->setTime(23, 59, 59);
            $query = $query
                ->andWhere('p.creatAt <= :dd')
                ->setParameter('dd', $search->dd);
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

        if (!empty($search->d) && $search->d instanceof \DateTime) {
            $query = $query
                ->andWhere('p.creatAt >= :d')
                ->setParameter('d', $search->d);
        }

        if (!empty($search->dd) && $search->dd instanceof \DateTime) {
            $search->dd->setTime(23, 59, 59);
            $query = $query
                ->andWhere('p.creatAt <= :dd')
                ->setParameter('dd', $search->dd);
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
    /**
     * Find list a prospect Unjoinable
     * @return Prospect[] 
     * @param SearchProspect $search
     * @return PaginationInterface 
     */
    public function findUnjoingChef(SearchProspect $search, User $user): PaginationInterface
    {
        $teams = $user->getTeams();
        if ($teams->isEmpty()) {
            return [];
        }

        $query = $this->createQueryBuilder('p')
            ->select('p, f')
            ->where('p.team IN (:teams)')
            ->setParameter('teams', $teams)
            ->leftJoin('p.relanceds', 'r')
            ->andWhere('r.motifRelanced = 2')

            // joiner les tables en relation ManyToOne avec team


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

        if (!empty($search->d) && $search->d instanceof \DateTime) {
            $query = $query
                ->andWhere('p.creatAt >= :d')
                ->setParameter('d', $search->d);
        }

        if (!empty($search->dd) && $search->dd instanceof \DateTime) {
            $search->dd->setTime(23, 59, 59);
            $query = $query
                ->andWhere('p.creatAt <= :dd')
                ->setParameter('dd', $search->dd);
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
    /**
     * Find list a prospect Unjoinable
     * @return Prospect[] 
     * @param SearchProspect $search
     * @return PaginationInterface 
     */
    public function findUnjoingCmrcl(SearchProspect $search, $id): PaginationInterface
    {


        $query = $this->createQueryBuilder('p')

            ->andWhere('p.comrcl = :val')
            ->setParameter('val', $id)
            ->leftJoin('p.relanceds', 'r')
            ->andWhere('r.motifRelanced = 2');

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

        if (!empty($search->g)) {
            $query = $query
                ->andWhere('p.email LIKE :g')
                ->setParameter('g', "%{$search->g}%");
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

        if (!empty($search->d) && $search->d instanceof \DateTime) {
            $query = $query
                ->andWhere('p.creatAt >= :d')
                ->setParameter('d', $search->d);
        }

        if (!empty($search->dd) && $search->dd instanceof \DateTime) {
            $search->dd->setTime(23, 59, 59);
            $query = $query
                ->andWhere('p.creatAt <= :dd')
                ->setParameter('dd', $search->dd);
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

        if (!empty($search->d) && $search->d instanceof \DateTime) {
            $query = $query
                ->andWhere('p.creatAt >= :d')
                ->setParameter('d', $search->d);
        }

        if (!empty($search->dd) && $search->dd instanceof \DateTime) {
            $search->dd->setTime(23, 59, 59);
            $query = $query
                ->andWhere('p.creatAt <= :dd')
                ->setParameter('dd', $search->dd);
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



    // afficher les prospects qui n ont pas du team et cmrcl
    /**
     * @return Prospect[] Returns an array of Prospect objects
     * 
     * @param SearchProspect $search
     * @return PaginationInterface 
     */
    public function findByUserPaAffecter(SearchProspect $search): PaginationInterface
    {

        // get selement les prospects qui n'as pas encors affectter a un user
        $query = $this->createQueryBuilder('p')
            ->select('p', 't', 'f')
            ->leftJoin('p.team', 't')
            ->leftJoin('p.comrcl', 'f')
            ->where('p.comrcl IS NULL')
            ->andWhere('p.team IS NULL')
            ->leftJoin('p.relanceds', 'r')
            ->andWhere('r.motifRelanced is null')
            ->orderBy('p.id', 'DESC');
        if (!empty($search->d) && $search->d instanceof \DateTime) {
            $query = $query
                ->andWhere('p.creatAt >= :d')
                ->setParameter('d', $search->d);
        }

        if (!empty($search->dd) && $search->dd instanceof \DateTime) {
            $search->dd->setTime(23, 59, 59);
            $query = $query
                ->andWhere('p.creatAt <= :dd')
                ->setParameter('dd', $search->dd);
        }

        return $this->paginator->paginate(
            $query,
            $search->page,
            10

        );
    }

    /**
     * getnumber prospects for notifiy
     */
    //return with int pour admin
    public function findAllNewProspectsApi(): int
    {
        $query = $this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->andWhere("p.comrcl is NULL")
            ->andWhere("p.team is NULL")
            ->leftJoin('p.relanceds', 'r')
            ->andWhere('r.motifRelanced is null');

        return (int) $query->getQuery()->getSingleScalarResult();
    }

    //return with int pour chef
    public function findAllNewProspectsChefApi(User $user): int
    {
        $team = $user->getTeams();
        if ($team->isEmpty()) {
            return [];
        }
        $query = $this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->leftJoin('p.team', 't')
            ->leftJoin('p.comrcl', 'f')
            ->where('p.team IN (:teams) ')
            ->setParameter('teams', $team)
            ->andWhere('p.team IS NOT NULL')
            ->andWhere('p.comrcl IS NULL OR p.comrcl = :user') // Filtrer les prospects no affectés et affect au chef aussi
            ->setParameter('user', $user);

        return (int) $query->getQuery()->getSingleScalarResult();
    }

    //return with int pour chef
    public function findAllNewProspectsComercialApi($id): int
    {

        $yesterday = new \DateTime('yesterday');
        $yesterday->setTime(23, 59, 59);
        $query = $this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->where('p.comrcl = :val')
            ->setParameter('val', $id)
            ->leftJoin('p.relanceds', 'r')
            ->andWhere('r.prospect IS NULL')
            ->leftJoin('p.histories', 'h')
            ->andWhere('h.actionDate >= :endOfYesterday')
            ->setParameter('endOfYesterday', $yesterday)

            ->andWhere('p.id NOT IN ( 
            SELECT pr.id FROM App\Entity\Prospect pr
            JOIN pr.relanceds rel
            WHERE rel.relacedAt > :endOfYesterday
        )')->setParameter('endOfYesterday', $yesterday);




        return (int) $query->getQuery()->getSingleScalarResult();
    }





    // public function findByCmrclAffecterApi($id): array
    // {

    //     $yesterday = new \DateTime('yesterday');
    //     $yesterday->setTime(23, 59, 59); // La fin de la journée d'hier 
    //     $query = $this->createQueryBuilder('p')
    //         ->select('DISTINCT p.id, p.name')
    //         //->leftJoin('p.relanceds', 'r')
    //         //->leftJoin('p.histories', 'h')
    //         ->where('p.comrcl = :val')
    //         //->andWhere('r.prospect IS NULL')
    //         //->andWhere('h.actionDate >= :endOfYesterday')
    //         ->setParameter('val', $id)
    //         ->setParameter('endOfYesterday', $yesterday)

    //         ->andWhere('p.id NOT IN ( 
    //             SELECT pr.id FROM App\Entity\Prospect pr
    //             JOIN pr.relanceds rel
    //             WHERE rel.relacedAt > :endOfYesterday
    //         )')->setParameter('endOfYesterday', $yesterday);

    //     return $query->getQuery()->getResult();
    // }



    /**
     * return prospect affect aux equipes du chef ou bien au chef meme
     * @return Prospect[] Returns an array of Prospect objects
     * 
     * @param SearchProspect $search
     * @return PaginationInterface 
     */
    public function findByChefAffecter(SearchProspect $search, User $user): PaginationInterface
    {
        //$team = $user->getTeams();
        // $today = new \DateTime();
        // $today->setTime(0, 0, 0);

        $team = $user->getTeams();
        if ($team->isEmpty()) {
            return [];
        }
        // get selement les prospects qui n'as pas encors affectter a un user
        $query = $this->createQueryBuilder('p')
            ->select('p, t, f')
            ->leftJoin('p.team', 't')
            ->leftJoin('p.comrcl', 'f')
            ->where('p.team IN (:teams) ')
            ->setParameter('teams', $team)
            ->andWhere('p.team IS NOT NULL')
            ->leftJoin('p.relanceds', 'r')
            ->andWhere('r.prospect IS NULL')
            ->andWhere('p.comrcl IS NULL OR p.comrcl = :val') // Filtrer les prospects no affectés et affect au chef aussi
            ->setParameter('val', $user)
            ->orderBy('p.id', 'DESC');



        if (!empty($search->d) && $search->d instanceof \DateTime) {
            $query = $query
                ->andWhere('p.creatAt >= :d')
                ->setParameter('d', $search->d);
        }

        if (!empty($search->dd) && $search->dd instanceof \DateTime) {
            $search->dd->setTime(23, 59, 59);
            $query = $query
                ->andWhere('p.creatAt <= :dd')
                ->setParameter('dd', $search->dd);
        }





        return $this->paginator->paginate(
            $query,
            $search->page,
            10

        );
    }



    // afficher les prospects qui sont affect au cmrcl
    /**
     * @return Prospect[] Returns an array of Prospect objects
     * 
     * @param SearchProspect $search
     * @return PaginationInterface 
     */
    public function findByCmrclAffecter(SearchProspect $search, $id): PaginationInterface
    {

        $startOfToday = new \DateTime('today');
        $yesterday = new \DateTime('yesterday');
        $yesterday->setTime(23, 59, 59); // La fin de la journée d'hier 
        $query = $this->createQueryBuilder('p')
            ->select('p')
            ->where('p.comrcl = :val')
            ->setParameter('val', $id)
            ->leftJoin('p.relanceds', 'r')
            ->andWhere('r.prospect IS NULL')
            ->leftJoin('p.histories', 'h')
            ->andWhere('h.actionDate >= :endOfYesterday')
            ->setParameter('endOfYesterday', $yesterday)
            ->orderBy('p.id', 'DESC');

        $query->andWhere('p.id NOT IN ( 
                SELECT pr.id FROM App\Entity\Prospect pr
                JOIN pr.relanceds rel
                WHERE rel.relacedAt > :startOfToday
            )')->setParameter('startOfToday', $yesterday);



        if (!empty($search->d) && $search->d instanceof \DateTime) {
            $query = $query
                ->andWhere('p.creatAt >= :d')
                ->setParameter('d', $search->d);
        }

        if (!empty($search->dd) && $search->dd instanceof \DateTime) {
            $search->dd->setTime(23, 59, 59);
            $query = $query
                ->andWhere('p.creatAt <= :dd')
                ->setParameter('dd', $search->dd);
        }



        return $this->paginator->paginate(
            $query,
            $search->page,
            10

        );
    }

    // afficher les prospects qui n'ont pas du team et cmrcl
    /**
     * @return Prospect[] Returns an array of Prospect objects
     * 
     * @param SearchProspect $search
     * @return PaginationInterface 
     */
    public function findByUserAffecterCmrcl(SearchProspect $search, $id): PaginationInterface
    {
        // $today = new \DateTime();
        // $today->setTime(0, 0, 0);
        // get selement les prospects qui n'as pas encors affectter a un user
        $query = $this->createQueryBuilder('p')
            ->select('p,  h')
            ->andWhere('p.comrcl = :val')
            ->setParameter('val', $id)
            ->leftJoin('p.relanceds', 'h')
            // ->andWhere('p.creatAt >= :startOfDay')
            // ->setParameter('startOfDay', $today)
            ->orderBy('p.id', 'DESC');




        if ((!empty($search->q))) {
            $query
                ->andWhere('p.name LIKE :q')

                ->setParameter('q', "%{$search->q}%");
        }
        if (isset($search->m)) {
            $query
                ->andWhere('p.lastname LIKE :m')
                ->setParameter('m', "%{$search->m}%");
        }

        if (isset($search->source)) {

            $query

                ->andWhere('p.source = :source')

                ->setParameter('source', $search->source);
        }

        if (isset($search->g)) {

            $query

                ->andWhere('p.email LIKE :g')

                ->setParameter('g', "%{$search->g}%");
        }

        if (isset($search->c)) {

            $query

                ->andWhere('p.city LIKE :c')

                ->setParameter('c', "%{$search->c}%");
        }

        if (isset($search->l)) {

            $query

                ->andWhere('p.phone LIKE :l')

                ->setParameter('l', "%{$search->l}%");
        }

        if (isset($search->s)) {

            $query

                ->andWhere('p.raisonSociale LIKE :s')

                ->setParameter('s', "%{$search->s}%");
        }


        if (!empty($search->motifRelanced)) {
            $query

                ->andWhere('h.motifRelanced = :motifRelanced')
                ->setParameter('motifRelanced', $search->motifRelanced);
        }


        if (!empty($search->d) && $search->d instanceof \DateTime) {
            $query
                ->andWhere('p.creatAt >= :d')
                ->setParameter('d', $search->d);
        }

        if (!empty($search->dd) && $search->dd instanceof \DateTime) {
            $search->dd->setTime(23, 59, 59);
            $query
                ->andWhere('p.creatAt <= :dd')
                ->setParameter('dd', $search->dd);
        }
        //cherche par relance

        if (!empty($search->dr) && $search->dr instanceof \DateTime) {
            $query
                ->andWhere('h.relacedAt >= :dr')
                ->setParameter('dr', $search->dr->format('Y-m-d'));
        }

        if (!empty($search->ddr) && $search->ddr instanceof \DateTime) {
            $search->ddr->setTime(23, 59, 59); // Fix time to end of the day
            $query
                ->andWhere('h.relacedAt <= :ddr')
                ->setParameter('ddr', $search->ddr->format('Y-m-d H:i:s'));
        }

        return $this->paginator->paginate(
            $query->getQuery(), // Exécuter la requête avec les filtres
            $search->page,
            10

        );
    }



    /**
     * a verifie deplcated
     * @return Prospect[] Returns an array of Prospect objects 
     * @param SearchProspect $searchfind
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

        if (!empty($search->d) && $search->d instanceof \DateTime) {
            $query = $query
                ->andWhere('p.creatAt >= :d')
                ->setParameter('d', $search->d);
        }

        if (!empty($search->dd) && $search->dd instanceof \DateTime) {
            $search->dd->setTime(23, 59, 59);
            $query = $query
                ->andWhere('p.creatAt <= :dd')
                ->setParameter('dd', $search->dd);
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



    /**
     * afecher seulement les prospects qui partient de panier du chef findByUserChefEquipe
     * deplicated voire emplacement
     * @return Prospect[] Returns an array of Prospect objects 
     * @param SearchProspect $search
     * @return PaginationInterface
     */
    public function findAllChefSearch(SearchProspect $search, User $user): PaginationInterface
    {
        // get selement les prospects qui n'as pas encors affectter a un user
        $teams = $user->getTeams();
        if ($teams->isEmpty()) {
            return [];
        }

        $query = $this
            ->createQueryBuilder('p')
            ->select('p,  h')

            // joiner les tables en relation ManyToOne avec team
            ->where('p.team IN (:teams)')
            // ->andWhere("p.comrcl is NULL")
            ->leftJoin('p.relanceds', 'h')
            ->setParameter('teams', $teams)
            ->orderBy('p.id', 'DESC');


        if ((!empty($search->q))) {
            $query = $query
                ->andWhere('p.name LIKE :q')

                ->setParameter('q', "%{$search->q}%");
        }

        if (!empty($search->d) && $search->d instanceof \DateTime && !empty($search->dd) && $search->dd instanceof \DateTime) {
            $search->dd->setTime(23, 59, 59); // Set end date to include the whole day
            $query = $query
                ->andWhere('p.creatAt >= :d')
                ->setParameter('d', $search->d)
                ->andWhere('p.creatAt <= :dd')
                ->setParameter('dd', $search->dd);
        }

        // if (!empty($search->d) && $search->d instanceof \DateTime) {
        //     $query = $query
        //         ->andWhere('p.creatAt >= :d')
        //         ->setParameter('d', $search->d);
        // }

        // if (!empty($search->dd) && $search->dd instanceof \DateTime) {
        //     $search->dd->setTime(23, 59, 59);
        //     $query = $query
        //         ->andWhere('p.creatAt <= :dd')
        //         ->setParameter('dd', $search->dd);
        // }


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
        //sherche par relance
        if (!empty($search->dr) && $search->dr instanceof \DateTime) {
            $query = $query
                ->andWhere('h.relacedAt >= :dr')
                ->setParameter('dr', $search->dr->format('Y-m-d'));
        }

        if (!empty($search->ddr) && $search->ddr instanceof \DateTime) {
            $search->ddr->setTime(23, 59, 59); // Fix time to end of the day
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
     * @return Prospect[] Returns an array of Prospect objects  
     */
    public function findOneByChef(User $user): array
    {
        $teams = $user->getTeams();

        if ($teams->isEmpty()) {
            return [];
        }

        $qb = $this->createQueryBuilder('p')
            ->where('p.team IN (:teams)')
            ->andWhere("p.comrcl is NOT NULL")
            ->setParameter('teams', $teams)
            ->orderBy('p.id', 'DESC');

        $prospects = $qb->getQuery()->getResult();

        return $prospects;




        // select * from prospect join user on prospect.comrcl_id = user.id where prospect.comrcl_id = 2;
    }
}
