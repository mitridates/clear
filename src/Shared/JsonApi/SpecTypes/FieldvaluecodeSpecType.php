<?php
namespace App\Shared\JsonApi\SpecTypes;

use App\Shared\JsonApi\Unserialize\JsonApiSpec;

/**
 * Entidad que, una vez registrada en JsonApiTypeRegistry, permite retornar
 * algún dato personalizado
 */
class FieldvaluecodeSpecType extends JsonApiSpec
{
    /**
     * @throws \Exception
     */
    public function __construct(array $data)
    {
        parent::__construct($data);
    }

    public function __toString(): string
    {
        return $this->attributes['value'];
    }
}
