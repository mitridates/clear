<?php

namespace App\Utils\Json\Serializers\Map;
use App\Entity\Map\Mapfurthergc;
use App\Utils\reflection\EntityReflectionHelper;
use App\vendor\tobscure\jsonapi\AbstractSerializer;
use App\vendor\tobscure\jsonapi\Relationship;
use App\vendor\tobscure\jsonapi\Resource;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

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