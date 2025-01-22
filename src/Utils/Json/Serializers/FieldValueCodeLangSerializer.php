<?php

namespace App\Utils\Json\Serializers;
use App\Entity\FieldDefinition\Fieldvaluecodelang;
use App\Utils\reflection\EntityReflectionHelper;
use App\vendor\tobscure\jsonapi\AbstractSerializer;

class FieldValueCodeLangSerializer extends AbstractSerializer
{
    protected $type = 'fieldvaluecode';
    protected ?array $fields;
    public function __construct(protected readonly ?string $locale= null)
    {
    }

    /**
     * @param Fieldvaluecodelang $model
     * @param ?array $fields
     * @return array
     */
    public function getAttributes($model, array $fields = null): array
    {
        return EntityReflectionHelper::serializeClassProperties($model, $fields);
    }

}