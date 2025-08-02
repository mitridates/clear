<?php
namespace App\Domain\Person\Manager;
use App\Domain\Person\Entity\Person;
use App\Manager\AbstractManager;
use App\Manager\Expr\ExprFilter;
use App\Shared\Paginator;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

class PersonManager extends AbstractManager
{
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($em, Person::class);
    }

    /**
     * Find by entity
     * @return array [Paginator, [result]]
     */
    public function paginate(Person $entity, array $listOptions): array
    {
        $alias = 'prsn';
        $qb = $this->repo->createQueryBuilder($alias);

        ExprFilter::addExprFilter($qb, $entity, $alias, [
            'like'=>['name', 'surname', 'id'],
            'eq'=>['country','admin1','admin2', 'admin3', 'organisation']
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
     * @param Person $person
     * @param string $alias
     * @return array|null
     */
    public function findByPerson(Person $person, string $alias= 'prsn'): ?array
    {
        $qb = $this->repo->createQueryBuilder($alias);

        ExprFilter::addExprFilter($qb, $person, $alias, [
                'or'=> ['name', 'surname'],
                'eq'=>['id']
            ]
        );
        return $qb
            ->getQuery()
            ->getResult();
    }
}
