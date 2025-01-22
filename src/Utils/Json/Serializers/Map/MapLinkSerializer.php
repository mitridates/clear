<?php

namespace App\Utils\Json\Serializers\Map;
use App\Entity\Map\Maplink;
use App\Utils\Json\Serializers\LinkSerializer;
use App\Utils\reflection\EntityReflectionHelper;
use App\vendor\tobscure\jsonapi\AbstractSerializer;
use App\vendor\tobscure\jsonapi\Relationship;
use App\vendor\tobscure\jsonapi\Resource;

class MapLinkSerializer extends AbstractSerializer
{
    use MapSerializerMTOTrait;

    protected $type = 'maplink';
    protected string $relationship='link';

    /**
     * @param Maplink $model
     * @param ?array $fields
     * @return array
     */
    public function getAttributes($model, array $fields = null): array
    {
        $data= EntityReflectionHelper::serializeClassProperties($model, $fields);
        $data['map']= $model->getMap()->getId();
        $data['link']= $model->getLink()->getId();
        return $data;
    }

    /**
     * @param Maplink $model
     * @return array
     */
    public function getLinks($model): array
    {
        $links= $this->getManyToOneLinks($model, $this->relationship);
        $links['link']= $this->urlGenerator->generate('admin_link_edit', ['id'=>$model->getLink()->getId()]);
        return $links;
    }

    /**
     * Relationship caved
     * @param Maplink $model
     * @return Relationship
     */
    public function link(Maplink $model): Relationship
    {
        return new Relationship(new Resource($model->getLink(), new LinkSerializer($this->urlGenerator)));
    }
}