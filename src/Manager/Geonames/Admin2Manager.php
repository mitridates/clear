<?php
namespace App\Manager\Geonames;
use App\Entity\Geonames\Admin2;
use App\Manager\AbstractManager;
use App\Manager\Expr\ExprFilter;
use App\Shared\Paginator;
use Doctrine\ORM\{EntityManagerInterface, NonUniqueResultException, NoResultException, Query\Expr};

class Admin2Manager extends AbstractManager
{
    public function __construct(protected EntityManagerInterface $em)
    {
        parent::__construct($em, Admin2::class);
    }

    public function findByAdmin2(Admin2 $admin2, string $alias= 'findByAdmin2'): ?array
    {
        $qb = $this->repo->createQueryBuilder($alias);
        return ExprFilter::addExprFilter($qb, $admin2, $alias, [
                'like'=> ['name', 'nameascii'],
                'eq'=>['id','geonameid', 'country', 'admin1']
            ])
            ->orderBy($alias.'.name', 'ASC')
            ->getQuery()
            ->getResult();
    }


    /**
     * Admin2 pager
     * @param Admin2 $admin2
     * @param int $page current page
     * @param int $ipp Items per page
     * @return array [Paginator, array]
     */
    public function pageByAdmin2(Admin2 $admin2, int $page, int $ipp): array
    {
        $alias = 'admin2';
        $qb = $this->repo->createQueryBuilder($alias);
        $expr = new Expr();
        ExprFilter::addExprFilter($qb, $admin2, $alias, [
                'like'=> ['name', 'nameascii'],
                'eq'=>['id','geonameid', 'country', 'admin1']
            ]
        );
        /**
         * SELECT to count results
         */
        try {
            $totalRows = $qb->add('select',$expr->count($alias.'.id'))->getQuery()->getSingleScalarResult();
        }catch (NoResultException|NonUniqueResultException){}

        $pager = new Paginator($page, $ipp, $totalRows);

        /**
         * Reset SELECT and get result
         */
        $limits = $pager->getLimits();

        $qb->resetDQLPart('select')
            ->add('select', $alias)
            ->setFirstResult($limits[0])//start
            ->setMaxResults($limits[1]);//end;

        return [$pager, $qb->getQuery()->getResult()];
    }

}
