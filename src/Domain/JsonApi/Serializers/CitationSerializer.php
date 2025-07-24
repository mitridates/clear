<?php

namespace App\Domain\JsonApi\Serializers;
use App\Domain\JsonApi\Serializers\Geonames\CountrySerializer;
use App\Entity\Citation\Citation;
use App\Shared\reflection\EntityReflectionHelper;
use App\Shared\tobscure\jsonapi\AbstractSerializer;
use App\Shared\tobscure\jsonapi\Relationship;
use App\Shared\tobscure\jsonapi\Resource;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CitationSerializer extends AbstractSerializer
{
    protected $type = 'citation';
    protected ?array $fields;

    public function __construct(protected readonly ?UrlGeneratorInterface $router)
    {
    }

    /**
     * @param Citation $model
     * @param array|null $fields
     * @return array
     */
    public function getAttributes(mixed $model, ?array $fields = null): array
    {
        $this->fields= $fields;
        $data= EntityReflectionHelper::serializeClassProperties($model, $fields);
        $data['jsondata']= json_decode($data['jsondata'], true);
        $data['cType']= $model->typeToString();
        return $data;
    }

    /**
     * Relationship country
     */
    public function country(Citation $model): ?Relationship
    {
        return $model->getCountry()? new Relationship(new Resource($model->getCountry(), new CountrySerializer())) : null;
    }

    public function getLinks(mixed $model): array
    {
        if(!$this->router) return [];
        return ['self'=>$this->router->generate('admin_citation_edit', ['id'=>$model->getId()])];
    }
}