<?php

namespace App\Utils\Json\Serializers\Map;
use App\Entity\Map\Mapdrafter;
use App\Utils\Json\Serializers\PersonSerializer;
use App\Utils\reflection\EntityReflectionHelper;
use App\vendor\tobscure\jsonapi\AbstractSerializer;
use App\vendor\tobscure\jsonapi\Relationship;
use App\vendor\tobscure\jsonapi\Resource;

class MapDrafterSerializer extends AbstractSerializer
{
    use MapSerializerMTOTrait;

    protected $type = 'mapdrafter';
    protected string $relationship='drafter';

    /**
     * @param Mapdrafter $model
     * @param ?array $fields
     * @return array
     */
    public function getAttributes($model, array $fields = null): array
    {
        $data= EntityReflectionHelper::serializeClassProperties($model, $fields);
        $item= 'drafterid';
        if(array_key_exists($item, $data) && gettype($data[$item])=== 'object'){
            $data[$item]= $data[$item]->getId();
        }
        $data['map']= $model->getMap()->getId();
        return $data;
    }


    /**
     * @param Mapdrafter $model
     * @return array
     */
    public function getLinks($model): array
    {
        $links= $this->getManyToOneLinks($model, $this->relationship);
        if($model->getDrafterid()){
            $links['drafterid']= $this->urlGenerator->generate('admin_person_edit', ['id'=>$model->getDrafterid()->getId()]);
        }
        return $links;
    }

    /**
     * Relationship drafterid
     * @param Mapdrafter $model
     * @return Relationship
     */
    public function drafterid(Mapdrafter $model): ?Relationship
    {
        return $model->getDrafterid()? new Relationship(new Resource($model->getDrafterid(), new PersonSerializer($this->urlGenerator))) : null;
    }
}