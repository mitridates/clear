<?php

namespace App\Domain\JsonApi\Serializers;
use App\Domain\Fielddefinition\Entity\Fieldvaluecodelang;
use App\Shared\reflection\EntityReflectionHelper;
use App\Shared\tobscure\jsonapi\AbstractSerializer;

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