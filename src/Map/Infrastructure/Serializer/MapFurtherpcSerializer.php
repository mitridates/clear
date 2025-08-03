<?php

namespace App\Map\Infrastructure\Serializer;
use App\Area\Infrastructure\Serializer\AreaSerializer;
use App\Geonames\Infrastructure\Serializer\Admin1Serializer;
use App\Geonames\Infrastructure\Serializer\CountrySerializer;
use App\Map\Domain\Entity\Map\Mapfurtherpc;
use App\Map\Infrastructure\Serializer\Trait\MapSerializerMTOTrait;
use App\Shared\reflection\EntityReflectionHelper;
use App\Shared\tobscure\jsonapi\AbstractSerializer;
use App\Shared\tobscure\jsonapi\Relationship;
use App\Shared\tobscure\jsonapi\Resource;

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