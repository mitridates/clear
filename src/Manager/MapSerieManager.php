<?php
namespace App\Manager;
use App\Entity\Mapserie;
use App\Manager\Expr\ExprFilter;
use App\Shared\Paginator;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

class MapSerieManager extends AbstractManager
{
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($em, Mapserie::class);
    }

    /**
     * Find by entity
     * @return array [Paginator, [result]]
     */
    public function paginate(Mapserie $entity, array $listOptions): array
    {
        $alias = 'mapserie';
        $qb = $this->repo->createQueryBuilder($alias);

        ExprFilter::addExprFilter($qb, $entity, $alias, [
            'like'=>['name', 'code', 'scale', 'abbreviation'],
            'eq'=>['publisher']
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
     * @param Mapserie $mapserie
     * @param string $alias
     * @return array|null
     */
    public function findByMapserie(Mapserie $mapserie, string $alias= 'findByAdmin1'): ?array
    {
        $qb = $this->repo->createQueryBuilder($alias);

        return ExprFilter::addExprFilter($qb, $mapserie, $alias, [
                'like'=> ['name'],
                'eq'=>['id']
            ]
        )
            ->getQuery()
            ->getResult();
    }
}
