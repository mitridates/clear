<?php

namespace App\Manager;
use App\Entity\Citation\Citation;
use App\Manager\Expr\ExprFilter;
use App\Utils\Paginator;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\Expr;

class CitationManager extends AbstractManager
{
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($em, Citation::class);
    }

//Filtrar JSON en mysql:
//SELECT `id`,`type`,  jsondata->'$.issued' AS issued   FROM `Citation`;
//SELECT `id`,`type`,  JSON_EXTRACT(`jsondata`, "$.issued") AS issued   FROM `Citation`;
//SELECT `id`,`type`, jsondata->'$.issued' AS issued FROM `Citation` WHERE jsondata->'$.issued.month' IS NOT NULL;
//SELECT `id`,`type`, jsondata->'$.issued' AS issued FROM `Citation` WHERE jsondata->'$.issued.year' = 2001;
//SELECT * FROM `citation` WHERE contributor->'$[*].lastname' LIKE '%Per%' OR contributor->'$[*].name' LIKE '%Fede%';
//LOWER CASE SELECT * FROM `citation` WHERE LOWER(contributor->'$[*].lastname') LIKE LOWER('%Per%') OR LOWER(contributor->'$[*].name') LIKE  LOWER('%FEDE%');
//Case & accent insensitive: SELECT * FROM `citation` WHERE LOWER(contributor->'$[*].lastname') COLLATE utf8mb4_0900_ai_ci LIKE LOWER('%Lopez%') OR LOWER(contributor->'$[*].name') COLLATE utf8mb4_0900_ai_ci LIKE  LOWER('%FEDE%');
    public function paginate(Citation $entity, array $listOptions): array
    {
        $alias = 'cit';

        $qb = $this->repo->createQueryBuilder($alias);
        ExprFilter::addExprFilter($qb, $entity, $alias, [
            'like'=>['title', 'subtitle', 'jsondata'],
            'eq'=>['type','country']
        ]);

        if($entity->getContributor()){
            $term= strtolower($entity->getContributor());
            $qb->orWhere("JSON_EXTRACT(LOWER(cit.contributor) , :lastname) like :val");
            $qb->orWhere("JSON_EXTRACT(LOWER(cit.contributor) , :firstname) like :val");
            $qb->orWhere("JSON_EXTRACT(LOWER(cit.contributor) , :orgname) like :val");
            $qb->setParameter(':val', '%'.$term.'%');
            $qb->setParameter(':lastname', '$[*].lastname');
            $qb->setParameter(':firstname', '$[*].firstname');
            $qb->setParameter(':orgname', '$[*].name');

        }
        /**
         * SELECT to count results
         */
        try {
            $listOptions['totalRows'] = $qb->add('select',$qb->expr()->count($alias.'.id'))->getQuery()->getSingleScalarResult();
        } catch (NoResultException|NonUniqueResultException) {
            return [new Paginator($listOptions['page'], $listOptions['ipp'], 0), []];
        }
//        var_dump($qb->getQuery()->getDQL());

        return $this->getPagination($qb, $entity, $alias, $listOptions);
    }

    /**
     * @param Citation $citation
     * @param string $alias
     * @return array|null
     */
    public function findByCitation(Citation $citation, string $alias= 'cit'): ?array
    {
        $qb = $this->repo->createQueryBuilder($alias);

        ExprFilter::addExprFilter($qb, $citation, $alias, [
                'or'=> ['title','subtitle'],
                'eq'=>['id']
            ]
        );

        return $qb
            ->getQuery()
            ->getResult();
    }
}
