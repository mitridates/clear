<?php

namespace App\Utils\Json\JsonApi;
use App\Utils\Json\JsonApi\SpecTypes\FieldvaluecodeSpecType;

class JsonApiManagerFactory {
    private static array $types = [];
    private static array $specs = [];

    public static function addSpec(string $type, callable $fn): void {
        self::addType($type, $fn);
        self::$specs[$type] = $fn;
    }

    public static function addType(string $type, callable $factory): void {
        self::$types[$type] = $factory;
    }

    public static function getSpec(string $type): ?callable {
        return self::$specs[$type] ?? null;
    }

    /**
     * @throws \Exception
     */
    public static function toJsonApiSpec(array $object): JsonApiSpec {
        if (isset(self::$types[$object['type']])) {
            return call_user_func(self::$types[$object['type']], $object);
        }
        return new JsonApiSpec($object);
    }

    public static function exists(string $type): bool {
        return isset(self::$types[$type]);
    }
}