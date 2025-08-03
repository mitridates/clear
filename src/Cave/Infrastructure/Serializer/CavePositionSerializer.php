<?php

namespace App\Cave\Infrastructure\Serializer;
use App\Cave\Domain\Entity\Caveposition;
use App\Shared\reflection\EntityReflectionHelper;
use App\Shared\tobscure\jsonapi\AbstractSerializer;
use App\Shared\tobscure\jsonapi\Relationship;
use App\Shared\tobscure\jsonapi\Resource;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CavePositionSerializer extends AbstractSerializer
{

    protected $type = 'caveposition';

    protected ?UrlGeneratorInterface $router;

    protected array $fields;

    public function __construct($router= null)
    {
        $this->router = $router;
    }

    /**
     * @param Caveposition $model
     * @param ?array $fields
     * @return array
     */
    public function getAttributes($model, array $fields = null): array
    {
        $data= EntityReflectionHelper::serializeClassProperties($model, $fields);
        $data['cave']= $model->getCave()->getId();
        return $data;
    }


    /**
     * Get the id.
     *
     * @param Caveposition $model
     *
     * @return string
     */
    public function getId($model): string
    {
        return $model->getCave()->getId();
    }

    /**
     * Relationship cave
     * @param Caveposition $model
     * @return Relationship
     */
    public function cave(Caveposition $model): ?Relationship
    {
        return new Relationship(new Resource($model->getCave(), new CaveSerializer($this->router)));
    }
}