<?php

namespace App\Utils\Json\Serializers\Geonames;
use App\Entity\Geonames\Country;
use App\Utils\reflection\EntityReflectionHelper;
use App\vendor\tobscure\jsonapi\AbstractSerializer;

class CountrySerializer extends AbstractSerializer
{

    protected $type = 'country';

    /**
     * @param Country|array $model
     * @param array|null $fields
     * @return array
     */
    public function getAttributes($model, ?array $fields = null): array
    {
        return EntityReflectionHelper::serializeClassProperties($model, $fields);
    }
}