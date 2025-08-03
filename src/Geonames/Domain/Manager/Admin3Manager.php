<?php
namespace App\Geonames\Domain\Manager;
use App\Geonames\Domain\Entity\Admin3;
use App\Shared\Manager\AbstractManager;
use App\Shared\Manager\Expr\ExprFilter;
use App\Shared\Paginator;
use Doctrine\ORM\{EntityManagerInterface, NonUniqueResultException, NoResultException, Query\Expr};

class Admin3Manager extends AbstractManager
{
    public function __construct(protected EntityManagerInterface $em)
    {
        parent::__construct($em, Admin3::class);
    }
    /**
     * @param Admin3 $admin3
     * @param string $alias
     * @return array|null
     */
    public function findByAdmin3(Admin3 $admin3, string $alias= 'findByAdmin3'): ?array
    {
        $qb = $this->repo->createQueryBuilder($alias);
        return ExprFilter::addExprFilter($qb, $admin3, $alias, [
                'like'=> ['name', 'nameascii'],
                'eq'=>['id','geonameid', 'country', 'admin1', 'admin2']
            ])
            ->orderBy($alias.'.name', 'ASC')
            ->getQuery()
            ->getResult();
    }


    /**
     * Admin3 pager
     * @param Admin3 $admin3
     * @param int $page current page
     * @param int $ipp Items per page
     * @return array [Paginator, array]
     */
    public function pageByAdmin3(Admin3 $admin3, int $page, int $ipp): array
    {
        $alias = 'admin3';
        $qb = $this->em->createQueryBuilder($alias);
        $expr = new Expr();
        ExprFilter::addExprFilter($qb, $admin3, $alias, [
                'like'=> ['name', 'nameascii'],
                'eq'=>['id','geonameid', 'country', 'admin1', 'admin2']
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