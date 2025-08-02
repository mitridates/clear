<?php
namespace App\Domain\SystemParameter\Manager;
use App\Domain\SystemParameter\Entity\SystemParameter;
use App\Manager\AbstractManager;
use App\Manager\Expr\ExprFilter;
use App\Shared\Paginator;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

class SystemParameterManager extends AbstractManager
{
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($em, SystemParameter::class);
    }

    /**
     * @param array $listOptions
     * <code>
     *   [
     *     'page'      => 1      // current page, default 1
     *     'ipp'       => 20,    // items per page, default 20
     *     'orderBy'   => null,  // order by column
     *     'sort'      => null,  // ASC/DESC
     *   ];
     *   </code>
     */
    public function paginate(SystemParameter $entity, array $listOptions): array
    {
        $alias = 'sys';
        $qb = $this->repo->createQueryBuilder($alias);

        ExprFilter::addExprFilter($qb, $entity, $alias, [
            'like'=>['name', 'language'],
            'eq'=>['country']
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

    public function getSystemParameter(): ?SystemParameter
    {
            return $this->repo->createQueryBuilder('sysp')
                ->select('sysp')
                ->setMaxResults(1)
                ->getQuery()
                ->getResult();
    }

    public function getSystemParameterValues(array $select): ?array
    {
        $qb= $this->repo->createQueryBuilder('sysp')
        ->select('sysp.id');

        foreach ($select as $item) {
            if(in_array($item, ['organisationdbm', 'organisationsite', 'country', 'mapserie', 'refunits', 'altitudeunit'])){
                $qb->addSelect('IDENTITY(sysp.'.$item.') AS '. $item);
            }else{
                $qb->addSelect('sysp.'.$item);
            }
        }

            return $qb->orderBy('sysp.id', 'ASC')
                ->setMaxResults(1)
                ->getQuery()
                ->getResult(AbstractQuery::HYDRATE_ARRAY);
    }

    public function getActiveSystemParameter(): ?SystemParameter
    {
            $active= $this->repo->createQueryBuilder('sysp')
                ->select('sysp')
                ->where('sysp.active = :number')
                ->setParameter(':number', 1)
                ->getQuery()->getResult();
            return (count($active))? $active[0]: null;
    }

    public function setActiveSystemParameter(SystemParameter $sysp)
    {
        $this->em->createQueryBuilder()
            ->update(SystemParameter::class, 'sysp')
            ->set('sysp.active', 0)
            ->where('sysp.id != :id')
            ->setParameter('id', $sysp->getId())
            ->getQuery()->execute();

        if(!$sysp->getActive()){
            $this->em->createQueryBuilder()
                ->update(SystemParameter::class, 'sysp')
                ->set('sysp.active', 1)
                ->where('sysp.id = :id')
                ->setParameter('id', $sysp->getId())
                ->getQuery()->execute();
        }

    }
}
