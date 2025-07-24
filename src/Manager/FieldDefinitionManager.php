<?php
namespace App\Manager;
use App\Entity\FieldDefinition\Fielddefinition;
use App\Entity\FieldDefinition\Fieldvaluecode;
use App\Manager\Expr\ExprFilter;
use App\Shared\Paginator;
use App\Shared\reflection\EntityReflectionHelper;
use Doctrine\ORM\{AbstractQuery, EntityManagerInterface, NonUniqueResultException, NoResultException, Query\Expr};

class FieldDefinitionManager extends AbstractManager
{
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($em, Fielddefinition::class);
    }

    /**
     * @return array [Paginator, [Fielddefinition]]
     */
    public function pageByFieldDefinition(Fielddefinition $entity, int $page, int $ipp, ?string $orderBy, ?string $sort, ?array $params=[]) : array
    {
        $alias = 'fd';
        $qb = $this->repo->createQueryBuilder($alias);
        $expr = new Expr();

        ExprFilter::addExprFilter($qb, $entity, $alias, [
                'like'=> ['id', 'name'],
                'eq'=>['entity','datatype','coding', 'singlemultivalued', 'valuecode']
            ]
        );

//https://stackoverflow.com/questions/39898530/doctrine-select-entities-with-relation-condition
    if(isset($params['review']) && $params['review']===1)
    {
        $reviews= $qb->expr()->andX(
            $qb->expr()->eq('tr.review', ':review') // someone saw the notification
        );
        $qb->leftJoin($alias.'.translations', 'tr')
            ->andWhere($reviews)
            ->setParameter('review',  1);
    }

        /**
         * SELECT to count results
         */
        try {
            $totalRows = $qb->add('select', $expr->count($alias . '.id'))->getQuery()->getSingleScalarResult();
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


        if($orderBy) ExprFilter::addSortExprFilter($qb, $entity, $alias, $orderBy, $sort);
        return [$pager, $qb->getQuery()->getResult()];
    }

    public function getValueCodesByField(int $vc): ?array
    {
        return $this->em->getRepository(Fieldvaluecode::class)->findBy(['field'=>$vc]);
    }

    public function getTranslationORFielddefinition(int $id, ?string $locale): ?Fielddefinition
    {
        $fd = $this->repo->find($id);
        if(!$locale || $locale=='en') return $fd;
        $fd_lang_manager= new FieldDefinitionLangManager($this->em, Fielddefinition::class);
        $trans= $fd_lang_manager->getTranslation($id, $locale);
        if(!$trans) return $fd;
        foreach (['name', 'abbreviation', 'definition', 'example', 'comment', 'uso'] as $item){
            if($trans->{'get'.  ucfirst($item)}() != null)
            {
                $fd->{'set'.  ucfirst($item)}($trans->{'get'.  ucfirst($item)}());
            }
        }
        return $fd;
    }


    /**
     * @return array|null [name][abbreviation]
     */
    public function getFieldDefinitionById(int $id, ?string $locale): ?array
    {
        $qb= $this->repo
            ->createQueryBuilder('q')
            ->select(['q.id', 'q.name', 'q.abbreviation'])
            ->where('q.id = :id')
            ->setParameter(':id', $id);

        if($locale==null){
            try {
                return $qb->getQuery()->getOneOrNullResult(AbstractQuery::HYDRATE_ARRAY);
            }catch (NonUniqueResultException){
                return null;
            }
        }else{
            $fdlm= new FieldDefinitionLangManager($this->em, Fielddefinitionlang::class);
            $name = $fdlm->getTranslatedFieldDQL('name', $id, $locale);
            $abbr = $fdlm->getTranslatedFieldDQL('abbreviation', $id, $locale);

            try {
                return $qb->addSelect('('.$name.') AS trans_name')
                    ->addSelect('('.$abbr.') AS trans_abbreviation')
                    ->setParameter(':locale', $locale)
                    ->getQuery()->getOneOrNullResult(AbstractQuery::HYDRATE_ARRAY);
            }catch (NonUniqueResultException){
                return null;
            }
        }
    }

    public function findByFielddefinition(Fielddefinition $entity, string $alias= 'fd'): array
    {
        $qb = $this->repo->createQueryBuilder($alias);

        ExprFilter::addExprFilter($qb, $entity, $alias, [
                'like'=> ['name']
            ]
        );
        if(is_numeric($entity->getName())){
            EntityReflectionHelper::setPrivateProperty($entity, 'id', $entity->getName());
            ExprFilter::addExprFilter($qb, $entity, $alias, [
                'eq'=> ['id']
            ]);
        }
        return $qb->getQuery()->getResult();
    }

}
