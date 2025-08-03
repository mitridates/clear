<?php
namespace App\Fielddefinition\Domain\Manager;
use App\Fielddefinition\Domain\Entity\Fielddefinitionlang;
use App\Shared\Manager\AbstractManager;
use App\Shared\Paginator;
use Doctrine\ORM\{AbstractQuery, EntityManagerInterface, NonUniqueResultException, NoResultException};

class FieldDefinitionLangManager extends AbstractManager
{
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($em, Fielddefinitionlang::class);
    }


    /**
     * Translations
     */
    public function getTranslation(int $id, string $locale): ?Fielddefinitionlang
    {
        $trans =  $this->repo
            ->createQueryBuilder('q')
            ->select('q')
            ->where('q.id = :id')
            ->andWhere('q.locale = :locale')
            ->setParameter(':id', $id)
            ->setParameter(':locale', $locale)
            ->setMaxResults(1)
            ->getQuery()->getResult(AbstractQuery::HYDRATE_OBJECT);
        return $trans ? $trans[0] : null;
    }


    /**
     * Paginate translations
     * @return array [Paginator, [result]]
     */
    public function paginateFdTranslationsById(int $id, int $page, int $ipp): array
    {
        $alias = 'fdl';
        $qb = $this->repo->createQueryBuilder($alias);
        $qb->add('where', $qb->expr()->eq($alias.'.id', ':id'))->setParameter(':id', $id);

        /**
         * SELECT to count results
         */
        try {
            $totalRows = $qb->add('select', $qb->expr()->count($alias . '.id'))->getQuery()->getSingleScalarResult();
        } catch (NoResultException|NonUniqueResultException) {
            return [new Paginator($page, $ipp, 0), []];
        }

        /**
         * Set paginator class
         */
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

    public function getTranslatedFieldDQL(string $field, int $id, string $locale): string
    {
        $alias = $field.'_'.$locale.'_alias';
        return $this->repo->createQueryBuilder($alias) ->select($alias.'.'.$field)
            ->where($alias.'.locale= :locale')
            ->andWhere($alias.'.id= :id')
            ->setParameter(':id', $id)
            ->setParameter(':locale', $locale)
            ->getDQL();
    }
}
