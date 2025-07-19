<?php

namespace App\Utils\Json\JsonApi;
class JsonApiManager {
    private array $data;
    private ?array $included;
    private array $ret = [];
    private bool $isParsed = false;
    private ?string $resource = null;

    public function __construct(array $data, ?array $included = null) {
        $this->data = $data;
        $this->included = $included;
    }

    /**
     * @return array
     */
    public function existsFactoryType($type): bool
    {
        return JsonApiManagerFactory::exists($type);
    }

    /**
     * @return JsonApiSpec[]|JsonApiSpec
     * @throws \Exception
     */
    public function getParsed(): array|JsonApiSpec {
        if (!$this->isParsed) {
            $this->parseResponse();
        }
        return $this->resource === 'document' ? $this->ret[0] : $this->ret;
    }

    /**
     * @throws \Exception
     */
    private function parseResponse(): void {
        if (isset($this->data['type'])) {
            $this->resource = 'document';
            $this->ret[] = $this->parseSpec(JsonApiManagerFactory::toJsonApiSpec($this->data));
        } else {
            $this->resource = 'collection';
            foreach ($this->data as $item) {
                $this->ret[] = $this->parseSpec(JsonApiManagerFactory::toJsonApiSpec($item));
            }
        }
        $this->isParsed = true;
    }

    private function parseSpec(JsonApiSpec $spec): JsonApiSpec {
        foreach ($spec->attributes as $key => &$attr) {
            if ($spec->relationships[$key]['data'] ?? null) {
                $attr = $this->getIncluded($spec->relationships[$key]['data'], $key, $spec);
            }

            if ($attr instanceof JsonApiSpec && $attr->relationships) {
                foreach ($attr->attributes as $subKey => &$subAttr) {
                    if (isset($attr->relationships[$subKey])) {
                        $subAttr = $this->getIncluded($attr->relationships[$subKey]['data'], $subKey, $attr);
                    }
                }
            }
        }

        if ($spec->meta) {
            foreach ($spec->meta as $key => &$metaItem) {
                if (is_array($metaItem) && isset($metaItem[0]) && JsonApiSpec::isSpec($metaItem[0])) {
                    foreach ($metaItem as &$item) {
                        $item = (new self($item))->getParsed();
                    }
                } elseif (isset($metaItem['data']) && is_array($metaItem['data'])) {
                    $included = $metaItem['included'] ?? null;
                    $metaItem = (new self($metaItem['data'], $included))->getParsed();
                }
            }
        }

        return $spec;
    }

    /**
     * @throws \Exception
     */
    private function getIncluded($data, string $key, JsonApiSpec $spec): ?JsonApiSpec {
        if (!$data) {
            trigger_error("Null relationship in $key for resource {$spec->id}", E_USER_WARNING);
            return null;
        }

        foreach ($this->included ?? [] as $inc) {
            if ($inc['type'] === $data['type'] && $inc['id'] === $data['id']) {
                return JsonApiManagerFactory::toJsonApiSpec($inc);
            }
        }

        return null;
    }


    public function findBy(array $criteria): array {
        $results = [];
        foreach ($this->getParsed() as $spec) {
            foreach ($criteria as $key => $expected) {
                $val = $spec->get($key);
                if (is_callable($expected)) {
                    if ($expected($val) !== false) {
                        $results[] = $spec;
                    }
                } elseif ($val === $expected) {
                    $results[] = $spec;
                }
            }
        }
        return $results;
    }

    public function findById($id): ?JsonApiSpec {
        foreach ($this->getParsed() as $spec) {
            if ((string)$spec->id === (string)$id) {
                return $spec;
            }
        }
        return null;
    }
}
