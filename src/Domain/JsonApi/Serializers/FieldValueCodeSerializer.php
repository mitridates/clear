<?php

namespace App\Domain\JsonApi\Serializers;
use App\Domain\Fielddefinition\Entity\Fieldvaluecode;
use App\Shared\reflection\EntityReflectionHelper;
use App\Shared\tobscure\jsonapi\AbstractSerializer;

class FieldValueCodeSerializer extends AbstractSerializer
{
    protected $type = 'fieldvaluecode';
    protected ?array $fields;

    /**
     * @param Fieldvaluecode $model
     * @param ?array $fields
     * @return array
     */
    public function getAttributes($model, array $fields = null): array
    {
        $data = EntityReflectionHelper::serializeClassProperties($model, $fields);
        unset($data['translations']);
        return $data;
    }

}