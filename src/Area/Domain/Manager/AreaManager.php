<?php
namespace App\Area\Domain\Manager;
use App\Area\Domain\Entity\Area;
use App\Shared\Manager\AbstractManager;
use App\Shared\Manager\Expr\ExprFilter;
use App\Shared\Paginator;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

class AreaManager extends AbstractManager
{
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($em, Area::class);
    }


    /**
     * Pager
     * @param Area $entity
     * @param array $listOptions
     * @return array [Paginator, [result]]
     */
    public function paginate(Area $entity, array $listOptions): array
    {
        $alias = 'area';
        $qb = $this->repo->createQueryBuilder($alias);

        ExprFilter::addExprFilter($qb, $entity, $alias, [
            'like'=>['name'],
            'eq'=>['id','country', 'admin1', 'code']
        ]);
        /**
         * SELECT to count results
         */
        try {
            $listOptions['totalRows'] = $qb->add('select',$qb->expr()->count($alias.'.id'))->getQuery()->getSingleScalarResult();
        } catch (NoResultException|NonUniqueResultException) {
            return [new Paginator($listOptions['page'], $listOptions['ipp'], 0), []];
        }
        return $this->getPagination($qb, $entity, $alias, $listOptions);
    }

    /**
     * @param Area $area
     * @param string $alias
     * @param array $select
     * @return array|null
     */
    public function findByArea(Area $area, string $alias= 'findByArea', array $select= []): ?array
    {
        $qb = $this->repo->createQueryBuilder($alias);

        return ExprFilter::addExprFilter($qb, $area, $alias, [
                'like'=> ['name'],
                'eq'=>['id','country', 'admin1']
            ]
        )
            ->orderBy($alias.'.name', 'ASC')
            ->getQuery()->getResult();
    }
}