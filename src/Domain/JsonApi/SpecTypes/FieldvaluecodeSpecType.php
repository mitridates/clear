<?php
namespace App\Domain\JsonApi\SpecTypes;

use App\Shared\JsonApi\Unserialize\JsonApiSpec;

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
