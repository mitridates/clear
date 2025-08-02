<?php
namespace App\Domain\Geonames\Manager;
use App\Domain\Geonames\Entity\Admin1;
use App\Shared\Manager\AbstractManager;
use App\Shared\Manager\Expr\ExprFilter;
use App\Shared\Paginator;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\Expr;

class Admin1Manager extends AbstractManager
{
    public function __construct(protected EntityManagerInterface $em)
    {
        parent::__construct($em, Admin1::class);
    }

    public function findByAdmin1(Admin1 $admin1, string $alias= 'findByAdmin1'): ?array
    {
        $qb = $this->repo->createQueryBuilder($alias);

        return ExprFilter::addExprFilter($qb, $admin1, $alias, [
                'like'=> ['name', 'nameascii'],
                'eq'=>['id','geonameid', 'country']

            ]
        )
        ->orderBy($alias.'.name', 'ASC')
        ->getQuery()
        ->getResult();
    }


    /**
     * Admin1 pager
     * @return array [Paginator, array]
     */
    public function pageByAdmin1(Admin1 $admin1, int $page, int $ipp): array
    {
        $alias = 'admin1';
        $qb = $this->repo->createQueryBuilder($alias);
        $expr = new Expr();
        ExprFilter::addExprFilter($qb, $admin1, $alias, [
                'like'=> ['name', 'nameascii'],
                'eq'=>['id','geonameid', 'country']
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
