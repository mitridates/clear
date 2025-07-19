<?php
namespace App\Utils\Json\JsonApi;
/**
 * JsonApiFactory::register('person', fn(array $data) => new Person($data));
 */
class JsonApiFactory
{
    /**
     * Lista de tipos registrados (type => callable)
     * @var array<string, callable>
     */
    protected static array $types = [];

    /**
     * Registra un tipo y su clase constructora
     *
     * @param string   $type
     * @param callable $creator function(array $data): JsonApiSpec
     * @return void
     */
    public static function register(string $type, callable $creator): void
    {
        self::$types[$type] = $creator;
    }

    /**
     * Crea una instancia de JsonApiSpec o clase hija según el tipo
     *
     * @param array $data
     * @return JsonApiSpec
     */
    public static function create(array $data): JsonApiSpec
    {
        if (isset($data['type'], self::$types[$data['type']])) {
            return call_user_func(self::$types[$data['type']], $data);
        }

        return new JsonApiSpec($data); // fallback genérico
    }

    /**
     * Verifica si hay una clase registrada para ese tipo
     *
     * @param string $type
     * @return bool
     */
    public static function has(string $type): bool
    {
        return isset(self::$types[$type]);
    }
}