<?php

namespace App\Domain\JsonApi\Serializers;
use App\Domain\Area\Entity\Area;
use App\Domain\JsonApi\Serializers\Geonames\Admin1Serializer;
use App\Domain\JsonApi\Serializers\Geonames\CountrySerializer;
use App\Shared\reflection\EntityReflectionHelper;
use App\Shared\tobscure\jsonapi\AbstractSerializer;
use App\Shared\tobscure\jsonapi\Relationship;
use App\Shared\tobscure\jsonapi\Resource;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class AreaSerializer extends AbstractSerializer
{
    protected $type = 'area';
    protected ?array $fields;

    public function __construct(protected readonly ?UrlGeneratorInterface $router)
    {
    }

    public function getAttributes(mixed $model, ?array $fields = null): array
    {
        $this->fields= $fields;
        $data= EntityReflectionHelper::serializeClassProperties($model, $fields);
        $identity= ['country', 'admin1'];
        $intersect= $fields?  array_intersect($fields ,$identity) : $identity;

        if(!$fields || !empty($intersect))
            foreach ($intersect as $v){
                $fn='get'.ucfirst(strtolower($v));
                $val= $model->$fn()? $model->$fn()->getId() : null;
                if($val){
                    $data[$v]=$val;
                }
            }
        return $data;
    }

    /**
     * Relationship country
     */
    public function country(Area $model): ?Relationship
    {
        return $model->getCountry()? new Relationship(new Resource($model->getCountry(), new CountrySerializer())) : null;
    }
    /**
     * Relationship Admin1
     */
    public function admin1(Area $model): ?Relationship
    {
        return $model->getAdmin1()? new Relationship(new Resource($model->getAdmin1(), new Admin1Serializer())) : null;
    }

    public function getLinks(mixed $model): array
    {
        if(!$this->router) return [];
        return ['self'=>$this->router->generate('admin_area_edit', ['id'=>$model->getId()])];
    }
}