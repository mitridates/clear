<?php
namespace App\Manager;
use App\Entity\Specie;
use App\Manager\Expr\ExprFilter;
use App\Utils\Paginator;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\Expr;

class SpecieManager extends AbstractManager
{
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($em, Specie::class);
    }

    /**
     * Find by entity
     * @return array [Paginator, [result]]
     */
    public function paginate(Specie $entity,  array $listOptions): array
    {
        $alias = 'spcie';
        $qb = $this->repo->createQueryBuilder($alias);

        ExprFilter::addExprFilter($qb, $entity, $alias, [
            'like'=>['name', 'commonname', 'genus', 'phylum', 'class', 'orden', 'family']
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
     * @param Specie $specie
     * @param string $alias
     * @return array|null
     */
    public function findBySpecie(Specie $specie, string $alias= 'spe'): ?array
    {
        $qb = $this->repo->createQueryBuilder($alias);
        $expr = new Expr();

        $qb->where($expr->like($alias.'.name', ':string'))
            ->orWhere($expr->like($alias.'.commonname', ':string'))
            ->setParameter(':string', '%'.$specie->getName().'%');

        return $qb
            ->getQuery()
            ->getResult();
    }
}