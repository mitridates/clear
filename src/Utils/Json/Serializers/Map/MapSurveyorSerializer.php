<?php

namespace App\Utils\Json\Serializers\Map;
use App\Entity\Map\Map;
use App\Entity\Map\Mapsurveyor;
use App\Entity\Map\Model\MapManyToOneInterface;
use App\Utils\Json\Serializers\PersonSerializer;
use App\Utils\reflection\EntityReflectionHelper;
use App\vendor\tobscure\jsonapi\AbstractSerializer;
use App\vendor\tobscure\jsonapi\Relationship;
use App\vendor\tobscure\jsonapi\Resource;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

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