<?php
namespace App\Domain\JsonApi\Serializers;
use App\Shared\tobscure\jsonapi\AbstractSerializer;

class MimeTypeSerializer extends AbstractSerializer
{
    protected $type = 'mimetype';

    /**
     * @param array $model
     * @param ?array $fields
     * @return array
     */
    public function getAttributes($model, array $fields = null): array
    {
        return [
            'name'=>key($model)
        ];
    }

    public function getId($model)
    {
        return key($model);
    }
}