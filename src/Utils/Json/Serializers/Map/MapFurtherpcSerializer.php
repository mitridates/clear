<?php

namespace App\Utils\Json\Serializers\Map;
use App\Entity\Map\Mapfurtherpc;
use App\Utils\Json\Serializers\AreaSerializer;
use App\Utils\Json\Serializers\Geonames\Admin1Serializer;
use App\Utils\Json\Serializers\Geonames\CountrySerializer;
use App\Utils\reflection\EntityReflectionHelper;
use App\vendor\tobscure\jsonapi\AbstractSerializer;
use App\vendor\tobscure\jsonapi\Relationship;
use App\vendor\tobscure\jsonapi\Resource;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class MapFurtherpcSerializer extends AbstractSerializer
{
    use MapSerializerMTOTrait;
    protected $type = 'mapfurtherpc';
    protected string $relationship='furtherpc';

    /**
     * @param Mapfurtherpc $model
     * @param ?array $fields
     * @return array
     */
    public function getAttributes($model, array $fields = null): array
    {
        $data= EntityReflectionHelper::serializeClassProperties($model, $fields);

        foreach (['country', 'admin1', 'area'] as $item){
            if(array_key_exists($item, $data) && gettype($data[$item])=== 'object'){
                $data[$item]= $data[$item]->getId();
            }
        }
        $data['map']= $model->getMap()->getId();
        return $data;
    }

    /**
     * @param Mapfurtherpc $model
     * @return array
     */
    public function getLinks($model): array
    {
        return $this->getManyToOneLinks($model, $this->relationship);
    }

    /**
     * Relationship country
     */
    public function country(Mapfurtherpc $model): ?Relationship
    {
        return $model->getCountry()? new Relationship(new Resource($model->getCountry(), new CountrySerializer())) : null;
    }

    /**
     * Relationship admin1
     */
    public function admin1(Mapfurtherpc $model): ?Relationship
    {
        return $model->getAdmin1()? new Relationship(new Resource($model->getAdmin1(), new Admin1Serializer())) : null;
    }

    /**
     * Relationship admin1
     */
    public function area(Mapfurtherpc $model): ?Relationship
    {
        return $model->getArea()? new Relationship(new Resource($model->getArea(), new AreaSerializer($this->urlGenerator))) : null;
    }
}