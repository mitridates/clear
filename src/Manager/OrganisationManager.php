<?php
namespace App\Manager;
use App\Entity\Organisation;
use App\Manager\Expr\ExprFilter;
use App\Shared\Paginator;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

class OrganisationManager extends AbstractManager
{
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($em, Organisation::class);
    }

    /**
     * Find by entity
     * @param Organisation $entity
     * @param array $listOptions
     * @return array [Paginator, [result]]
     */
    public function paginate(Organisation $entity, array $listOptions): array
    {
        $alias = 'org';
        $qb = $this->repo->createQueryBuilder($alias);

        ExprFilter::addExprFilter($qb, $entity, $alias, [
            'like'=>['name', 'initials', 'code', 'id'],
            'eq'=>['country', 'type', 'coverage', 'grouping', 'admin1', 'admin2', 'admin3']
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

    public function findByOrganisation(Organisation $organisation, string $alias= 'fbo'): ?array
    {
        $qb = $this->repo->createQueryBuilder($alias);

        return ExprFilter::addExprFilter($qb, $organisation, $alias, [
                'like'=> ['name'],
                'eq'=>['id', 'country', 'admin1', 'admin2', 'admin3']
            ]
        )
        ->getQuery()
        ->getResult();
    }

    /**
     * Count ID generators, exclude active ID generator organisation.
     * @param Organisation|null $exclude
     * @return int
     */
    public function countIdGenerators(Organisation $exclude = null): int
    {
        $alias='idg';
        $qb = $this->repo->createQueryBuilder($alias)
            ->select('count('.$alias.'.id)')
            ->where($alias.'.isgenerator = :number')
            ->setParameter(':number', 1);
        if($exclude instanceof Organisation){
            $qb->andWhere($alias.'.id != :excludeId')
                ->setParameter(':excludeId', $exclude->getId());
        }
        try {
            return $qb->getQuery()
                ->getSingleScalarResult();
        } catch (NoResultException|NonUniqueResultException) {
            return 0;
        }
    }

    /**
     * @return array
     */
    public function getIdGenerators(): array
    {
        return $this->repo->createQueryBuilder('o')
            ->select(['o.id', 'o.name', 'o.code', 'IDENTITY(o.country)'])
            ->where('o.isgenerator = :number')
            ->setParameter(':number', 1)
            ->getQuery()->getArrayResult();
    }
}