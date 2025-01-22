<?php

namespace App\Utils\Json\Serializers;
use App\Entity\FieldDefinition\Fieldvaluecode;
use App\Utils\reflection\EntityReflectionHelper;
use App\vendor\tobscure\jsonapi\AbstractSerializer;

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