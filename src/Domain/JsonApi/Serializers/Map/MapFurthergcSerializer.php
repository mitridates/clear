<?php

namespace App\Domain\JsonApi\Serializers\Map;
use App\Domain\JsonApi\Serializers\Map\Trait\MapSerializerMTOTrait;
use App\Domain\Map\Entity\Map\Mapfurthergc;
use App\Shared\reflection\EntityReflectionHelper;
use App\Shared\tobscure\jsonapi\AbstractSerializer;

class MapFurthergcSerializer extends AbstractSerializer
{
    use MapSerializerMTOTrait;
    protected $type = 'mapfurthergc';
    protected string $relationship='furthergc';

    /**
     * @param Mapfurthergc $model
     * @param ?array $fields
     * @return array
     */
    public function getAttributes($model, array $fields = null): array
    {
        $data= EntityReflectionHelper::serializeClassProperties($model, $fields);
        $data['map']= $model->getMap()->getId();
        return $data;
    }

    /**
     * @param Mapfurthergc $model
     * @return array
     */
    public function getLinks($model): array
    {
        return $this->getManyToOneLinks($model, $this->relationship);
    }
}