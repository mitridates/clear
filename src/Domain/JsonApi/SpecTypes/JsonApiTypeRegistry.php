<?php

namespace App\Domain\JsonApi\SpecTypes;

use App\Shared\JsonApi\Unserialize\JsonApiManagerFactory;

/**
 * Registra los tipos de este proyecto
 */
class JsonApiTypeRegistry
{
    public static function registerAll(): void
    {
        JsonApiManagerFactory::addType('fieldvaluecode', fn(array $data) => new FieldvaluecodeSpecType($data));
        JsonApiManagerFactory::addType('person', fn(array $data) => new PersonSpecType($data));
        //JsonApiManagerFactory::addType('organization', fn($data) => new OrganizationSpec($data));
        // Agrega aquí todos los tipos
    }
}