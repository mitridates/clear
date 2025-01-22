<?php
namespace App\Manager\Expr;
use Doctrine\ORM\{Query\Expr,QueryBuilder};

class ExprFilter
{
    public static function addSortExprFilter(QueryBuilder &$qb, object|string $entity, string $alias, ?string $orderBy=null, ?string $sort=null): void
    {
        $elms= explode(',', $orderBy);
        $sort= $sort==='DESC'? 'DESC' : 'ASC';
        if(!$elms) return;
        foreach ($elms as $elm){
            if(property_exists($entity, $elm)) {
                $qb->orderBy(new Expr\OrderBy($alias.'.'.$elm, $sort));
            }
        }
    }

    /**
     * Add simple Expr comparison to QueryBuilder from array [like=>[property, ...], eq=>[property, ...]]
     */
    public  static function addExprFilter(QueryBuilder &$qb, object $entity, string $alias, array $exprFields ): QueryBuilder
    {
        $expr = new Expr();
        $andX = $expr->andX();
        $orX = $expr->orX();


        $exprFields = array_merge_recursive($exprFields, ['like' => [], 'eq' => [], 'or' => []]);
        $params = [];

        //name LIKE value
        foreach ($exprFields['like'] as $k) {
            if ($entity->{'get' . ucfirst($k)}() != null) {
                $andX->add($expr->like($alias . '.' . $k, ':' . $k));
                $params[':' . $k] = '%' . $entity->{'get' . ucfirst($k)}() . '%';
            }
        }


        if (count($exprFields['or'])){
            foreach ($exprFields['or'] as $k) {
                if ($entity->{'get' . ucfirst($k)}() != null) {
                    $orX->add($expr->like($alias . '.' . $k, ':' . $k));
                    $params[':' . $k] = '%' . $entity->{'get' . ucfirst($k)}() . '%';
                }
            }
            $andX->add($orX);
        }
        //name = value
            foreach ($exprFields['eq'] as $k) {
                if ($entity->{'get' . ucfirst($k)}() != null) {
                    $andX->add($expr->eq($alias . '.' . $k, ':' . $k));
                    $params[':' . $k] = $entity->{'get' . ucfirst($k)}();
                }
            }

        //WHERE
        if($andX->count()>0){
            $qb->add('where', $andX)->setParameters($params);
        }
        return $qb;
    }}
