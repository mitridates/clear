<?php
namespace App\Utils\Json\JsonApi\SpecTypes;

use App\Utils\Json\JsonApi\JsonApiSpec;

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
