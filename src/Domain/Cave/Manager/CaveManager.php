<?php
namespace App\Domain\Cave\Manager;
use App\Domain\Cave\Entity\Cave;
use App\Domain\Map\Entity\Map\Mapcave;
use App\Domain\Map\Entity\Map\Mapimage;
use App\Shared\Manager\AbstractManager;
use App\Shared\Manager\Expr\ExprFilter;
use App\Shared\Paginator;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\PersistentCollection;
use Doctrine\ORM\Query\Expr;

class CaveManager extends AbstractManager
{
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($em, Cave::class);
    }

    /**
     * Find by Cave
     * @param Cave $entity
     * @param int $page     current page
     * @param int $ipp Items per page
     * @param ?string $orderBy - Column
     * @param ?string $sort ASC/DESC
     * @return array [Paginator, [result]]
     */
    public function pageByCave(Cave $entity, int $page, int $ipp, ?string $orderBy, ?string $sort) : array
    {
        $alias = 'cave';
        $qb = $this->repo->createQueryBuilder($alias);
        $expr = new Expr();
        $andx = $expr->andX();
        $params = [];

        //name LIKE value
        foreach(['id',/* 'name',**/ 'serial'] as $k)
        {
            if($entity->{'get'.  ucfirst($k)}()!=null)
            {
                $andx->add($expr->like($alias.'.'.$k, ':'.$k));
                $params[':'.$k] = '%'.$entity->{'get'.  ucfirst($k)}().'%';
            }
        }

        if($entity->getName()){
            $andx->add( 'cave.name LIKE :name
                          OR cnames.name LIKE :name');
            $qb->leftJoin('cave.cavename', 'cnames');
            $params[':name'] = '%'.$entity->getName().'%';
        }

        //name = value
        foreach(['country','admin1','admin2','admin3','area','featuretype', 'entrancetype','penetrability',
                    'lengthcategory', 'depthcategory', 'entrancemarking','updatestatus'] as $k)
        {
            if($entity->{'get'.  ucfirst($k)}()!=null)
            {
                $andx->add($expr->eq($alias.'.'.$k, ':'.$k));
                $params[':'.$k] = $entity->{'get'.  ucfirst($k)}();
            }
        }

        //WHERE
        if($andx->count()>0){
            $qb->add('where', $andx)->setParameters($params);
        }

        /**
         * SELECT to count results
         */
        try {
            $totalRows = $qb->add('select', $qb->expr()->count($alias . '.code'))->getQuery()->getSingleScalarResult();
        } catch (NoResultException|NonUniqueResultException) {
            return [new Paginator($page, $ipp, 0), []];
        }

        if($page===0){//no pagination, show all
            $page=1;
            $ipp=$totalRows;
        }
        $pager = new Paginator($page, $ipp, $totalRows);

        /**
         * Reset SELECT and get result
         */
        $limits = $pager->getLimits();

        $qb->resetDQLPart('select')
            ->add('select', $alias)
            ->setFirstResult($limits[0])//start
            ->setMaxResults($limits[1]);//end;

        if($orderBy) ExprFilter::addSortExprFilter($qb, $entity, $alias, $orderBy, $sort);
//        var_dump($qb->getDQL());
        return [$pager, $qb->getQuery()->getResult()];
    }

    /**
     * @param Cave $entity
     * @param PersistentCollection $mto
     * @param int $page
     * @param int $ipp
     * @param string|null $orderBy
     * @param string|null $sort
     * @return array [Paginator, [result]]
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function pageManyToOne(Cave $entity, PersistentCollection $mto, int $page, int $ipp, ?string $orderBy, ?string $sort): array
    {

        $alias = 'cavemanytoone';
        $class= $mto->getTypeClass()->newInstance();
        $qb = $this->getEm()->getRepository(get_class($class))->createQueryBuilder($alias);
        $expr = new Expr();
        $andx = $expr->andX();
        $params = [];

        /**
         * SELECT to count results
         */
        $qb->add('select',$expr->count($alias.'.cave'));
        $andx->add($expr->eq($alias.'.cave', ':cave'));
        $params[':cave'] = $entity->getId();
        $qb->add('where', $andx)->setParameters($params);

        try {
            $totalRows = $qb->add('select', $qb->expr()->count($alias . '.sequence'))->getQuery()->getSingleScalarResult();
        } catch (NoResultException|NonUniqueResultException) {
            return [new Paginator($page, $ipp, 0), []];
        }

        $pager = new Paginator($page, $ipp, $totalRows);


        /**
         * Reset SELECT and get result
         */
        $limits = $pager->getLimits();
        $qb->resetDQLPart('select')
            ->add('select', $alias)
            ->setFirstResult($limits[0])//start
            ->setMaxResults($limits[1]);//end;

        if($orderBy) ExprFilter::addSortExprFilter($qb, $class, $alias, $orderBy, $sort);

        return [$pager, $qb->getQuery()->getResult()];
    }

    /**
     * Find by Cave
     * @param  $id
     * @param array $toCount [name, namespace]
     * @return array
     */
    public function countManyToOne($id, array $toCount)
    {
        $last= array_key_last($toCount);
        $select=  'SELECT  c.id AS id, ';
        foreach($toCount as $name=>$class)
        {
            $a= 'al'.$name;
            $w= $a.'.cave=:caveid';
            $s= '(SELECT COUNT('.$a.') FROM '.$class.' '.$a.' WHERE '.$w.') AS '.$name;
            $select.= $name!==$last? $s.',' : $s;
        }
        $select.=' FROM Cavern:Cave c WHERE c.id=:caveid';
        $em = $this->repo->getEm();
        return $em->createQuery($select)
            ->setParameter('caveid', $id)
            ->getSingleResult();
    }

    /**
     * @param Cave $cave
     * @return int
     */
    public function countMapimageByCave(Cave $cave): int
    {
        $alias = 'mi';
        $qb = $this->getEm()->getRepository(Mapimage::class)->createQueryBuilder($alias);
        $expr = new Expr();

        /**
         * SELECT to count results
         */
        $qb->add('select',$expr->count($alias.'.sequence'))
            ->innerJoin(Mapcave::class, 'mc')
            ->where('mc.cave = :caveid')
            ->andWhere('mc.map = mi.map')
            ->setParameter('caveid', $cave->getId())
        ;

        try {
            return $qb->getQuery()->getSingleScalarResult();
        } catch (NoResultException|NonUniqueResultException $e) {
            return 0;
        }
    }

    /**
     * @param Cave $cave
     * @param string $alias
     * @return array|null
     */
    public function findByCave(Cave $cave, string $alias= 'findcave'): ?array
    {
        $qb = $this->repo->createQueryBuilder($alias);

        return ExprFilter::addExprFilter($qb, $cave, $alias, [
                'like'=> ['name'],
                'eq'=>['id']
            ]
        )
            ->getQuery()
            ->getResult();
    }



    /**
     * @inheritDoc
     */
    public function getClass(): string
    {
        return Cave::class;
    }
}

