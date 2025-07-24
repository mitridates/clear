<?php

namespace App\Domain\JsonApi\Serializers\Map;
use App\Domain\JsonApi\Serializers\FieldValueCodeSerializer;
use App\Domain\JsonApi\Serializers\Map\Trait\MapSerializerMTOTrait;
use App\Entity\Map\Mapimage;
use App\Shared\reflection\EntityReflectionHelper;
use App\Shared\tobscure\jsonapi\AbstractSerializer;
use App\Shared\tobscure\jsonapi\Relationship;
use App\Shared\tobscure\jsonapi\Resource;

class MapImageSerializer extends AbstractSerializer
{
    use MapSerializerMTOTrait;
    protected $type = 'mapimage';
    protected string $relationship='image';

    /**
     * @param Mapimage $model
     * @param ?array $fields
     * @return array
     * @throws \ReflectionException
     */
    public function getAttributes($model, array $fields = null): array
    {
        $data= EntityReflectionHelper::serializeClassProperties($model, $fields);
        $identity= ['map', 'digitaltechnique', 'type', 'format', 'map'];
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
     * @param Mapimage $model
     * @return array
     */
    public function getLinks($model): array
    {
        return $this->getManyToOneLinks($model, $this->relationship);
    }

    /**
     * Relationship Fieldvaluecode
     */
    public function digitaltechnique(Mapimage $model): ?Relationship
    {
        return $model->getDigitaltechnique()? new Relationship(new Resource($model->getDigitaltechnique(), new FieldValueCodeSerializer($this->locale))) : null;
    }

    /**
     * Relationship Fieldvaluecode
     */
    public function type(Mapimage $model): ?Relationship
    {
        return $model->getType()? new Relationship(new Resource($model->getType(), new FieldValueCodeSerializer())) : null;
    }

    /**
     * Relationship Fieldvaluecode
     */
    public function format(Mapimage $model): ?Relationship
    {
        return $model->getFormat()? new Relationship(new Resource($model->getFormat(), new FieldValueCodeSerializer())) : null;
    }

    /**
     * Relationship map
     * @param Mapimage $model
     * @return ?Relationship
     */
    public function map(Mapimage $model): ?Relationship
    {
        return new Relationship(new Resource($model->getMap(), new MapSerializer($this->urlGenerator)));
    }
}