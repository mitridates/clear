<?php

namespace App\Domain\JsonApi\Serializers\cave;
use App\Domain\Cave\Entity\Cavecontent;
use App\Domain\JsonApi\Serializers\FieldValueCodeSerializer;
use App\Shared\reflection\EntityReflectionHelper;
use App\Shared\tobscure\jsonapi\AbstractSerializer;
use App\Shared\tobscure\jsonapi\Relationship;
use App\Shared\tobscure\jsonapi\Resource;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CaveContentSerializer extends AbstractSerializer
{

    protected $type = 'cavecontent';

    protected ?UrlGeneratorInterface $router;

    protected ?string $csrfToken;

    protected ?string $locale;

    protected array $fields;

    public function __construct($router= null, $csrfToken= null, $locale=null)
    {
        $this->router = $router;
        $this->csrfToken= $csrfToken;
        $this->locale= $locale;
    }

    /**
     * @param Cavecontent $model
     * @param ?array $fields
     * @return array
     */
    public function getAttributes($model, array $fields = null): array
    {
        $data= EntityReflectionHelper::serializeClassProperties($model, $fields);
        $data['cave']= $model->getCave()->getId();
        $data['content']= $model->getContent()->getId();
        return $data;
    }


    /**
     * @param Cavecontent $model
     * @return array
     */
    public function getLinks($model): array
    {
        if(!$this->router) return [];
        $links=[];
        $links['self']= $this->router->generate('dashboard_cave_updatemanytoone',  [
            'cave'=>$model->getCave()->getId(),
            'name'=>'content',
            'sequence'=>$model->getSequence()
        ]);
        if($this->csrfToken){

            $links['DELETE']= $this->router->generate('dashboard_cave_deletemanytoone', [
                'cave'=>$model->getCave()->getId(),
                'sequence'=>$model->getSequence(),
                'name'=>'content',
                'deletetoken'=>$this->csrfToken
            ]);
        }
        $links['cave']= $this->router->generate('dashboard_cave_edit', ['id'=>$model->getCave()->getId()]);
        return $links;
    }


    /**
     * Get the id.
     *
     * @param Cavecontent $model
     *
     * @return int
     */
    public function getId($model): int
    {
        return $model->getSequence();
    }

    /**
     * Relationship cave
     * @param Cavecontent $model
     * @return Relationship
     */
    public function cave(Cavecontent $model): ?Relationship
    {
        return new Relationship(new Resource($model->getCave(), new CaveSerializer($this->router)));
    }
    /**
     * Relationship Fieldvaluecode
     * @param Cavecontent $model
     * @return Relationship
     */
    public function content(Cavecontent $model): ?Relationship
    {
        return new Relationship(new Resource($model->getContent(), new FieldValueCodeSerializer($this->locale)));
    }
}