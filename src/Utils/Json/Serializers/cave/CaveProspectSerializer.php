<?php

namespace App\Utils\Json\Serializers\cave;
use App\Entity\Cave\Caveprospect;
use App\Utils\reflection\EntityReflectionHelper;
use App\Utils\Json\Serializers\FieldValueCodeSerializer;
use App\vendor\tobscure\jsonapi\AbstractSerializer;
use App\vendor\tobscure\jsonapi\Relationship;
use App\vendor\tobscure\jsonapi\Resource;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CaveProspectSerializer extends AbstractSerializer
{

    protected $type = 'caveprospect';

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
     * @param Caveprospect $model
     * @param ?array $fields
     * @return array
     */
    public function getAttributes($model, array $fields = null): array
    {
        $data= EntityReflectionHelper::serializeClassProperties($model, $fields);
        $data['cave']= $model->getCave()->getId();
        $data['importance']= $model->getProspect()->getId();
        return $data;
    }


    /**
     * @param Caveprospect $model
     * @return array
     */
    public function getLinks($model): array
    {
        if(!$this->router) return [];
        $links=[];
        $links['self']= $this->router->generate('dashboard_cave_updatemanytoone',  [
            'cave'=>$model->getCave()->getId(),
            'name'=>'prospect',
            'sequence'=>$model->getSequence()
        ]);
        if($this->csrfToken){

            $links['DELETE']= $this->router->generate('dashboard_cave_deletemanytoone', [
                'cave'=>$model->getCave()->getId(),
                'sequence'=>$model->getSequence(),
                'name'=>'prospect',
                'deletetoken'=>$this->csrfToken
            ]);
        }
        $links['cave']= $this->router->generate('dashboard_cave_edit', ['id'=>$model->getCave()->getId()]);
        return $links;
    }


    /**
     * Get the id.
     *
     * @param Caveprospect $model
     *
     * @return int
     */
    public function getId($model): int
    {
        return $model->getSequence();
    }

    /**
     * Relationship cave
     * @param Caveprospect $model
     * @return Relationship
     */
    public function cave(Caveprospect $model): ?Relationship
    {
        return new Relationship(new Resource($model->getCave(), new CaveSerializer($this->router)));
    }
    /**
     * Relationship Fieldvaluecode
     * @param Caveprospect $model
     * @return Relationship
     */
    public function prospect(Caveprospect $model): ?Relationship
    {
        return new Relationship(new Resource($model->getProspect(), new FieldValueCodeSerializer($this->locale)));
    }
}