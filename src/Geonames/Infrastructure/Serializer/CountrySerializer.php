<?php

namespace App\Geonames\Infrastructure\Serializer;
use App\Geonames\Domain\Entity\Country;
use App\Shared\reflection\EntityReflectionHelper;
use App\Shared\tobscure\jsonapi\AbstractSerializer;

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