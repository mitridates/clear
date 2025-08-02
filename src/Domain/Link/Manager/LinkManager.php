<?php

namespace App\Domain\Link\Manager;
use App\Domain\Link\Entity\Link;
use App\Shared\Manager\AbstractManager;
use App\Shared\Manager\Expr\ExprFilter;
use App\Shared\Paginator;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\Expr;

class LinkManager extends AbstractManager
{
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($em, Link::class);
    }


    public function paginate(Link $entity, array $listOptions): array
    {
        $alias = 'lin';
        $qb = $this->repo->createQueryBuilder($alias);
        ExprFilter::addExprFilter($qb, $entity, $alias, [
            'like'=>['organisationname', 'authorname', 'title'],
            'eq'=>['organisation','author']
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
     * @param Link $link
     * @param string $alias
     * @return array|null
     */
    public function findByLink(Link $link, string $alias= 'web'): ?array
    {
        $qb = $this->repo->createQueryBuilder($alias);

        ExprFilter::addExprFilter($qb, $link, $alias, [
                'like'=> ['title'],
                'eq'=>['id']
            ]
        );
        return $qb
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Link $link
     * @param string $alias
     * @return bool
     */
    public function existsUrl(Link $link, string $alias= 'web'): bool
    {
        $expr = new Expr();
        $qb= $this->repo->createQueryBuilder($alias);
        $qb->add('select', $expr->count($alias . '.id'))
            ->where($expr->eq($alias.'.url', ':url'))
            ->setParameter('url', $link->getUrl());
        if($link->getId()!==null){
            $qb->andWhere($expr->notIn($alias.'.id', ':id'));
            $qb->setParameter('id', $link->getId());
        }
        try {
            return $qb->getQuery()->getSingleScalarResult() !== 0;
        } catch (NoResultException $e) {
            return false;
        } catch (NonUniqueResultException $e) {
            return true;
        }
    }

    /**
     * @inheritDoc
     */
    public function getClass(): string
    {
        return Link::class;
    }
}
