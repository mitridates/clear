<?php

namespace App\Domain\JsonApi\Serializers;
use App\Entity\Link;
use App\Shared\reflection\EntityReflectionHelper;
use App\Shared\tobscure\jsonapi\AbstractSerializer;
use App\Shared\tobscure\jsonapi\Relationship;
use App\Shared\tobscure\jsonapi\Resource;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class LinkSerializer extends AbstractSerializer
{
    protected $type = 'link';
    private ?array $fields;
    public function __construct(
        protected  ?UrlGeneratorInterface $urlGenerator= null
    ){}

    /**
     * @param Link $model
     * @param ?array $fields
     * @return array
     */
    public function getAttributes($model, ?array $fields = null): array
    {
        $data= EntityReflectionHelper::serializeClassProperties($model, $fields);
        $identity= ['author', 'organisation'];
        $intersect= $fields?  array_intersect($fields ,$identity) : $identity;
        if(!$fields || !empty($intersect))
            foreach ($intersect as $v){
                $fn='get'.ucfirst(strtolower($v));
                $val= $model->$fn()? $model->$fn()->getName() : null;
                if($val){
                    $data[$v]=$val;
                }
            }
        if($model->getAccessed()){
            $data['accessed']=$model->getAccessed()->format('Y-m-d');
        }
        return $data;
    }
    
    /**
     * Relationship author
     */
    public function author(Link $model): ?Relationship
    {
        return $model->getAuthor()? new Relationship(new Resource($model->getAuthor(), new PersonSerializer($this->urlGenerator))) : null;
    }

    /**
     * Relationship Organisation
     */
    public function organisation(Link $model): ?Relationship
    {
        return $model->getOrganisation()? new Relationship(new Resource($model->getOrganisation(), new OrganisationSerializer($this->urlGenerator))) : null;
    }
    public function getLinks($model): array
    {
        if(!$this->urlGenerator) return [];
        return ['self'=>$this->urlGenerator->generate('admin_link_edit', ['id'=>$model->getId()])];
    }
}