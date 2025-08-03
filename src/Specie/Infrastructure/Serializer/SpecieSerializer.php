<?php

namespace App\Specie\Infrastructure\Serializer;
use App\Shared\reflection\EntityReflectionHelper;
use App\Shared\tobscure\jsonapi\AbstractSerializer;
use App\Specie\Domain\Entity\Specie;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class SpecieSerializer extends AbstractSerializer
{

    protected $type = 'specie';
    protected ?array $fields;

    public function __construct(protected readonly ?UrlGeneratorInterface $router= null)
    {
    }

    /**
     * @param Specie $model
     * @param ?array $fields
     * @return array
     */
    public function getAttributes($model, ?array $fields = null): array
    {
        return EntityReflectionHelper::serializeClassProperties($model, $fields);
    }

    /**
     * @param Specie $model
     * @return array
     */
    public function getLinks($model): array
    {
        if(!$this->router) return [];
        $links['self']= $this->router->generate('admin_specie_edit', ['id'=>$model->getId()]);
        return $links;
    }
}