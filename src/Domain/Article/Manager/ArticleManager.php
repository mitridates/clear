<?php

namespace App\Domain\Article\Manager;
use App\Domain\Article\Entity\Article;
use App\Shared\Manager\AbstractManager;
use App\Shared\Manager\Expr\ExprFilter;
use App\Shared\Paginator;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

class ArticleManager extends AbstractManager
{
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($em, Article::class);
    }

    public function paginate(Article $entity, array $listOptions): array
    {
        $alias = 'art';
        $qb = $this->repo->createQueryBuilder($alias);
        ExprFilter::addExprFilter($qb, $entity, $alias, [
            'like'=>['publicationyear', 'authororeditor', 'id', 'articlename', 'publicationname'],
            'eq'=>['country','admin1']
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
     * @param Article $article
     * @param string $alias
     * @return array|null
     */
    public function findByArticle(Article $article, string $alias= 'art'): ?array
    {
        $qb = $this->repo->createQueryBuilder($alias);

        ExprFilter::addExprFilter($qb, $article, $alias, [
                'or'=> ['articlename','publicationname'],
                'eq'=>['id']
            ]
        );

        return $qb
            ->getQuery()
            ->getResult();
    }
}
