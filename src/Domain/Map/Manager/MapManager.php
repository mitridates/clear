<?php
namespace App\Domain\Map\Manager;
use App\Domain\Map\Entity\Map\Map;
use App\Shared\Manager\AbstractManager;
use App\Shared\Manager\Expr\ExprFilter;
use App\Shared\Paginator;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;


class MapManager extends AbstractManager
{

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($em, Map::class);
    }


    public function paginate(Map $entity, array $listOptions): array
    {
        $alias = 'map';
        $qb = $this->repo->createQueryBuilder($alias);
        ExprFilter::addExprFilter($qb, $entity, $alias, [
            'like'=>['id','name'],
            'eq'=>['sourcecountry','country','admin1','admin2','admin3','sourceorg','type','sourcetype','mapserie', 'area']
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

    public function paginateRelationship(object $entity, array $listOptions, ?array $exprFieldsFilter = []): array
    {
        $repo= $this->em->getRepository(get_class($entity));
        $alias = 'mapmto';
        $qb = $repo->createQueryBuilder($alias);
        ExprFilter::addExprFilter($qb, $entity, $alias,
//            $exprFieldsFilter
            ['eq'=>['map']]
        );

        /**
         * SELECT to count results
         */
        try {
            $listOptions['totalRows'] = $qb->add('select',$qb->expr()->count($alias.'.map'))->getQuery()->getSingleScalarResult();
        } catch (NoResultException|NonUniqueResultException) {}
        if(empty($listOptions['totalRows'])){
            return [new Paginator($listOptions['page'], $listOptions['ipp'], 0), []];
        }
        return $this->getPagination($qb, $entity, $alias, $listOptions);
    }


//    /**
//     * Find by entity
//     *
//     * @param Map $entity
//     * @param int $page current page
//     * @param int $ipp Items per page
//     * @param ?string $orderBy - Column
//     * @param ?string $sort ASC/DESC
//     * @return array [Paginator, [result]]
//     */
//    public function pageByMap(Map $entity, int $page, int $ipp, ?string $orderBy, ?string $sort): array
//    {
//        $alias = 'map';
//        $qb = $this->repo->createQueryBuilder($alias);
//        $expr = new Expr();
//        $andx = $expr->andX();
//        $params = [];
//
//        //name LIKE value
//        foreach(['id', 'name'] as $k)
//        {
//            if($entity->{'get'.  ucfirst($k)}()!=null)
//            {
//                $andx->add($expr->like($alias.'.'.$k, ':'.$k));
//                $params[':'.$k] = '%'.$entity->{'get'.  ucfirst($k)}().'%';
//            }
//        }
//
//        //name = value
//        foreach(['sourcecountry','country','admin1','admin2','admin3','sourceorg','type','sourcetype','mapserie', 'area'] as $k)
//        {
//            if($entity->{'get'.  ucfirst($k)}()!=null)
//            {
//                $andx->add($expr->eq($alias.'.'.$k, ':'.$k));
//                $params[':'.$k] = $entity->{'get'.  ucfirst($k)}();
//            }
//        }
//
//        //WHERE
//        if($andx->count()>0){
//            $qb->add('where', $andx)->setParameters($params);
//        }
//
//        /**
//         * SELECT to count results
//         */
//        try {
//            $totalRows = $qb->add('select',$expr->count($alias.'.id'))->getQuery()->getSingleScalarResult();
//        } catch (NoResultException|NonUniqueResultException) {
//            return [new Paginator($page, $ipp, 0), []];
//        }
//
//        $pager = new Paginator($page, $ipp, $totalRows);
//
//        /**
//         * Reset SELECT and get result
//         */
//        $limits = $pager->getLimits();
//
//        $qb->resetDQLPart('select')
//            ->add('select', $alias)
//            ->setFirstResult($limits[0])//start
//            ->setMaxResults($limits[1]);//end;
//
//        if($orderBy) ExprFilter::addSortExprFilter($qb, $entity, $alias, $orderBy, $sort);
//
//        return [$pager, $qb->getQuery()->getResult()];
//    }
//
//    /**
//     * @param Map $entity
//     * @param PersistentCollection $mto
//     * @param int $page
//     * @param int $ipp
//     * @param string|null $orderBy
//     * @param string|null $sort
//     * @return array [Paginator, [result]]
//     */
//    public function pageManyToOne(Map $entity, PersistentCollection $mto, int $page, int $ipp, ?string $orderBy, ?string $sort): array
//    {
//
//        $alias = 'mapmanytoone';
//        $class= $mto->getTypeClass()->newInstance();
//        $qb = $this->em->getRepository(get_class($class))->createQueryBuilder($alias);
//        $expr = new Expr();
//        $andx = $expr->andX();
//        $params = [];
//
//        /**
//         * SELECT to count results
//         */
//        $qb->add('select',$expr->count($alias.'.map'));
//        $andx->add($expr->eq($alias.'.map', ':map'));
//        $params[':map'] = $entity->getId();
//        $qb->add('where', $andx)->setParameters($params);
//
//        /**
//         * SELECT to count results
//         */
//        try {
//            $totalRows = $qb->add('select',$expr->count($alias.'.sequence'))->getQuery()->getSingleScalarResult();
//        } catch (NoResultException|NonUniqueResultException) {
//            return [new Paginator($page, $ipp, 0), []];
//        }
//
//
//        $pager = new Paginator($page, $ipp, $totalRows);
//
//
//        /**
//         * Reset SELECT and get result
//         */
//        $limits = $pager->getLimits();
//        $qb->resetDQLPart('select')
//            ->add('select', $alias)
//            ->setFirstResult($limits[0])//start
//            ->setMaxResults($limits[1]);//end;
//
//        if($orderBy) ExprFilter::addSortExprFilter($qb, $class, $alias, $orderBy, $sort);
//
//        return [$pager, $qb->getQuery()->getResult()];
//    }
//
//    /**
//     * @param Cave $cave
//     * @param int $page
//     * @param int $ipp
//     * @param string|null $orderBy
//     * @param string|null $sort
//     * @return array [Paginator, [result]]
//     */
//    public function pageMapimageByCave(Cave $cave,  int $page, int $ipp, ?string $orderBy, ?string $sort): array
//    {
//
//        $alias = 'mi';
//        $qb = $this->em->getRepository(Mapimage::class)->createQueryBuilder($alias);
//        $expr = new Expr();
//
//        /**
//         * SELECT to count results
//         */
//        $qb->add('select',$expr->count($alias.'.sequence'))
//        ->innerJoin(Mapcave::class, 'mc')
//        ->where('mc.cave = :caveid')
//        ->andWhere('mc.map = mi.map')
//        ->setParameter('caveid', $cave->getId())
//        ;
//
//        try {
//            $totalRows = $qb->getQuery()->getSingleScalarResult();
//        } catch (NoResultException|NonUniqueResultException) {
//            return [new Paginator($page, $ipp, 0), []];
//        }
//
//        $pager = new Paginator($page, $ipp, $totalRows);
//
//        /**
//         * Reset SELECT and get result
//         */
//        $limits = $pager->getLimits();
//        $qb->resetDQLPart('select')
//            ->add('select', $alias)
//            ->setFirstResult($limits[0])//start
//            ->setMaxResults($limits[1]);//end;
//
//        if($orderBy) ExprFilter::addSortExprFilter($qb, Mapimage::class, $alias, $orderBy, $sort);
//        return [$pager, $qb->getQuery()->getResult()];
//    }

//    /**
//     * @param Map $map
//     * @param string $alias
//     * @return array|null
//     */
//    public function findByMap(Map $map, string $alias= 'findmap'): ?array
//    {
//        $qb = $this->em->createQueryBuilder($alias);
//        return ExprFilter::addExprFilter($qb, $map, $alias, ['like'=> ['name'],'eq'=>['id']])
//            ->getQuery()
//            ->getResult();
//    }

}