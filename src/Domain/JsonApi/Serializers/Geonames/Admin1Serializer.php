<?php

namespace App\Domain\JsonApi\Serializers\Geonames;
use App\Domain\Geonames\Entity\Admin1;
use App\Shared\reflection\EntityReflectionHelper;
use App\Shared\tobscure\jsonapi\AbstractSerializer;
use App\Shared\tobscure\jsonapi\Relationship;
use App\Shared\tobscure\jsonapi\Resource;

class Admin1Serializer extends AbstractSerializer
{

    protected $type = 'admin1';

    /**
     * @param Admin1 $model
     * @param ?array $fields
     * @return array
     */
    public function getAttributes($model, array $fields = null): array
    {
        $data= EntityReflectionHelper::serializeClassProperties($model, $fields);
        if($fields && in_array('country', $fields) && $data['country']){
            $data['country']= $model->getCountry()->getName();
        }
        return $data;
    }

    /**
     * Relationship country
     * @param Admin1 $model
     * @return Relationship
     */
    public function country(Admin1 $model): ?Relationship
    {
        return $model->getCountry()? new Relationship(new Resource($model->getCountry(), new CountrySerializer())) : null;
    }

}