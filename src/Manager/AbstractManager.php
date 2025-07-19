<?php
namespace App\Manager;
use App\Manager\Expr\ExprFilter;
use App\Utils\Paginator;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\MappingException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;

abstract class AbstractManager
{
    public EntityRepository $repo;

    public function __construct(protected EntityManagerInterface $em, private readonly string $class) {

        $this->repo = $em->getRepository($this->class);
    }

    /**
     * Basic rows count
     * @return int
     */
    public function count(): int
    {
        $meta = $this->em->getClassMetadata($this->class);
        try {
            $idFieldName= $meta->getSingleIdentifierFieldName();
            $qb= $this->repo->createQueryBuilder('counter')->select(sprintf('count(counter.%s)', $idFieldName));
            return $qb->getQuery()->getSingleScalarResult();
        }catch (MappingException|NoResultException|NonUniqueResultException){
            return 0;
        }
    }

    /**
     * Get Pagination class & array result
     * @param array $options
     * <code>
     *  [
     *    'page'      => 1      // current page, default 1
     *    'ipp'       => 20,    // items per page, default 20
     *    'totalRows' => 0,     // total items, default 0
     *    'orderBy'   => null,  // order by column
     *    'sort'      => null,  // ASC/DESC
     *  ];
     *  </code>
     * @return array
     * <code>
     *   [
     *      Paginator,  // Paginator class
     *      []          // result array
     *   ];
     *   </code>
     */
    public function getPagination(QueryBuilder &$qb, object $entity, string $alias, array $options=[]): array
    {
        $o = array_replace_recursive(['page'=>1, 'ipp'=>20, 'totalRows'=>0, 'orderBy'=>null, 'sort'=>null], $options);
        $pager = new Paginator($o['page'], $o['ipp'], $o['totalRows']);

        /**
         * Reset SELECT and get result
         */
        $limits = $pager->getLimits();

        $qb->resetDQLPart('select')
            ->add('select', $alias)
            ->setFirstResult($limits[0])//start
            ->setMaxResults($limits[1]);//end;

        if($o['orderBy']) ExprFilter::addSortExprFilter($qb, $entity, $alias, $o['orderBy'], $o['sort']);


        return [$pager, $qb->getQuery()->getResult()];
    }
}
