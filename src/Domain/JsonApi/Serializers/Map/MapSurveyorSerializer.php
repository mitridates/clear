<?php

namespace App\Domain\JsonApi\Serializers\Map;
use App\Domain\JsonApi\Serializers\Map\Trait\MapSerializerMTOTrait;
use App\Domain\JsonApi\Serializers\PersonSerializer;
use App\Entity\Map\Mapsurveyor;
use App\Shared\reflection\EntityReflectionHelper;
use App\Shared\tobscure\jsonapi\AbstractSerializer;
use App\Shared\tobscure\jsonapi\Relationship;
use App\Shared\tobscure\jsonapi\Resource;

class MapSurveyorSerializer extends AbstractSerializer
{
    use MapSerializerMTOTrait;
    protected $type = 'mapsurveyor';
    protected string $relationship='surveyor';

    /**
     * @param Mapsurveyor $model
     * @param ?array $fields
     * @return array
     */
    public function getAttributes($model, array $fields = null): array
    {
        $data= EntityReflectionHelper::serializeClassProperties($model, $fields);
        $item= 'surveyorid';
        if(in_array($item, $data) && $data[$item]){
            $data[$item]= $data[$item]->getId();
        }
        $data['map']= $model->getMap()->getId();
        return $data;
    }


    /**
     * @param Mapsurveyor $model
     * @return array
     */
    public function getLinks($model): array
    {
        $links= $this->getManyToOneLinks($model, $this->relationship);
        if($model->getSurveyorid()){
            $links['surveyorid']= $this->urlGenerator->generate('admin_person_edit', ['id'=>$model->getSurveyorid()->getId()]);
        }
        return $links;
    }

    /**
     * Relationship surveyorid
     * @param Mapsurveyor $model
     * @return Relationship|null
     */
    public function surveyorid(Mapsurveyor $model): ?Relationship
    {
        return $model->getSurveyorid()? new Relationship(new Resource($model->getSurveyorid(), new PersonSerializer($this->urlGenerator))) : null;
    }
}