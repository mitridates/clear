<?php

namespace App\Map\Infrastructure\Serializer;
use App\Citation\Infrastructure\Serializer\CitationSerializer;
use App\Map\Domain\Entity\Map\Mapcitation;
use App\Map\Infrastructure\Serializer\Trait\MapSerializerMTOTrait;
use App\Shared\reflection\EntityReflectionHelper;
use App\Shared\tobscure\jsonapi\AbstractSerializer;
use App\Shared\tobscure\jsonapi\Relationship;
use App\Shared\tobscure\jsonapi\Resource;

class MapCitationSerializer extends AbstractSerializer
{
    use MapSerializerMTOTrait;

    protected $type = 'mapcitation';
    protected string $relationship='citation';

    /**
     * @param Mapcitation $model
     * @param ?array $fields
     * @return array
     */
    public function getAttributes($model, array $fields = null): array
    {
        $data= EntityReflectionHelper::serializeClassProperties($model, $fields);
        $data['map']= $model->getMap()->getId();
        $data['citation']= $model->getCitation()->getId();
        return $data;
    }
    /**
     * @param Mapcitation $model
     * @return array
     */
    public function getLinks($model): array
    {
        $links= $this->getManyToOneLinks($model, $this->relationship);
        $links['citation']= $this->urlGenerator->generate('admin_citation_edit', ['id'=>$model->getCitation()->getId()]);
        return $links;
    }

    /**
     * Relationship article
     * @param Mapcitation $model
     * @return ?Relationship
     */
    public function citation(Mapcitation $model): ?Relationship
    {
        return $model->getCitation()? new Relationship(new Resource($model->getCitation(), new CitationSerializer($this->urlGenerator))) : null;
    }
}