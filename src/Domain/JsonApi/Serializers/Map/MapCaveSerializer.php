<?php

namespace App\Domain\JsonApi\Serializers\Map;
use App\Domain\JsonApi\Serializers\cave\CaveSerializer;
use App\Domain\JsonApi\Serializers\Map\Trait\MapSerializerMTOTrait;
use App\Domain\Map\Entity\Map\Mapcave;
use App\Shared\reflection\EntityReflectionHelper;
use App\Shared\tobscure\jsonapi\AbstractSerializer;
use App\Shared\tobscure\jsonapi\Relationship;
use App\Shared\tobscure\jsonapi\Resource;

class MapCaveSerializer extends AbstractSerializer
{
    use MapSerializerMTOTrait;
    protected $type = 'mapcave';
    protected string $relationship='cave';

    /**
     * @param Mapcave $model
     * @param ?array $fields
     * @return array
     */
    public function getAttributes($model, array $fields = null): array
    {
        $data= EntityReflectionHelper::serializeClassProperties($model, $fields);
        $data['map']= $model->getMap()->getId();
        $data['cave']= $model->getCave()->getId();
        return $data;
    }

    /**
     * @param Mapcave $model
     * @return array
     */
    public function getLinks($model): array
    {
        $links= $this->getManyToOneLinks($model, $this->relationship);
        $links['cave']= $this->urlGenerator->generate('admin_citation_edit', ['id'=>$model->getCave()->getId()]);
        return $links;
    }

    /**
     * Relationship caved
     * @param Mapcave $model
     * @return Relationship
     */
    public function cave(Mapcave $model): ?Relationship
    {
        return $model->getCave()? new Relationship(new Resource($model->getCave(), new CaveSerializer($this->urlGenerator))) : null;
    }
}