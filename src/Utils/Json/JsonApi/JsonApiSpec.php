<?php

namespace App\Utils\Json\JsonApi;


class JsonApiSpec {
    public string $id;
    public string $type;
    public array $attributes;
    public ?array $meta;
    public ?array $links;
    public ?array $relationships;

    public function __construct(array $data) {
        if (!self::isSpec($data)) {
            throw new \Exception('The supplied resource is not a formatted JSON:API array');
        }
        $this->id = $data['id'];
        $this->type = $data['type'];
        $this->attributes = $data['attributes'];
        $this->meta = $data['meta'] ?? null;
        $this->links = $data['links'] ?? null;
        $this->relationships = $data['relationships'] ?? null;
    }

    public function __toString(): string {
        return $this->attributes['name'] ?? $this->id;
    }

    public function getLink(?string $key = 'self'): ?string {
        return $this->links[$key] ?? null;
    }

    /**
     * Get attribute(s) or computed value
     * @param string|array|callable $s
     * @return mixed
     */
    public function get($s) {
        if (is_array($s)) {
            $ret = [];
            foreach ($s as $key) {
                $ret[$key] = $this->get($key);
            }
            return $ret;
        }

        if (is_string($s)) {
            if ($s === 'id') {
                return $this->id;
            } elseif ($s === '_type_') {
                return $this->type;
            } else {
                return $this->attributes[$s] ?? null;
            }
        }

        if (is_callable($s)) {
            return $s($this->attributes);
        }

        return null;
    }

    /**
     * Set an attribute
     * @param string $key
     * @param mixed $value
     * @return self
     */
    public function set(string $key, $value): self {
        $this->attributes[$key] = $value;
        return $this;
    }

    /**
     * Set a meta value
     * @param string $key
     * @param mixed $value
     * @return self
     */
    public function setMeta(string $key, $value): self {
        if ($this->meta === null) {
            $this->meta = [];
        }
        $this->meta[$key] = $value;
        return $this;
    }

    /**
     * Get a meta value
     * @param string $key
     * @return mixed|null
     */
    public function getMeta(string $key): mixed {
        return $this->meta[$key] ?? null;
    }

    /**
     * Check if a value is a valid JSON:API resource object
     * @param mixed $data
     * @return bool
     */
    public static function isSpec($data): bool {
        return is_array($data) &&
            isset($data['type'], $data['attributes'], $data['id']);
    }

}