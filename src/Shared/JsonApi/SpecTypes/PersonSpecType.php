<?php
namespace App\Shared\JsonApi\SpecTypes;

use App\Shared\JsonApi\Unserialize\JsonApiSpec;

/**
 * Entidad que, una vez registrada en JsonApiTypeRegistry, permite retornar
 * algún dato personalizado
 */
class PersonSpecType extends JsonApiSpec
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
        $name = $this->attributes['name'] ?? '';
        $surname = $this->attributes['surname'] ?? '';
        return trim("$name $surname") ?: $this->id;
    }

    // Puedes agregar otros métodos específicos para `Person` aquí:
    public function getFullName(): string
    {
        return $this->__toString();
    }

    public function getInitials(): string
    {
        $name = $this->attributes['name'] ?? '';
        $surname = $this->attributes['surname'] ?? '';
        return strtoupper(substr($name, 0, 1) . substr($surname, 0, 1));
    }
}
