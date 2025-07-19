<?php

namespace App\Utils\Json\JsonApi\SpecTypes;

use App\Utils\Json\JsonApi\JsonApiManagerFactory;

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