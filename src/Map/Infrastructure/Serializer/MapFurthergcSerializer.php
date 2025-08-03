<?php

namespace App\Map\Infrastructure\Serializer;
use App\Map\Domain\Entity\Map\Mapfurthergc;
use App\Map\Infrastructure\Serializer\Trait\MapSerializerMTOTrait;
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