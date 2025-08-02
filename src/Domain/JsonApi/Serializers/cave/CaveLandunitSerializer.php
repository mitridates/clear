<?php

namespace App\Domain\JsonApi\Serializers\cave;
use App\Domain\Cave\Entity\Cavelandunit;
use App\Shared\reflection\EntityReflectionHelper;
use App\Shared\tobscure\jsonapi\AbstractSerializer;
use App\Shared\tobscure\jsonapi\Relationship;
use App\Shared\tobscure\jsonapi\Resource;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CaveLandunitSerializer extends AbstractSerializer
{

    protected $type = 'cavelandunit';

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
     * @param Cavelandunit $model
     * @param ?array $fields
     * @return array
     */
    public function getAttributes($model, array $fields = null): array
    {
        $data = EntityReflectionHelper::serializeClassProperties($model, $fields);
        if (in_array('cave', $data)) $data['cave'] = $model->getCave()->getId();
        return $data;
    }


    /**
     * @param Cavelandunit $model
     * @return array
     */
    public function getLinks($model): array
    {
        if(!$this->router) return [];
        $links=[];
        $links['self']= $this->router->generate('dashboard_cave_updatemanytoone',  [
            'cave'=>$model->getCave()->getId(),
            'name'=>'landunit',
            'sequence'=>$model->getSequence()
        ]);
        if($this->csrfToken){

            $links['DELETE']= $this->router->generate('dashboard_cave_deletemanytoone', [
                'cave'=>$model->getCave()->getId(),
                'sequence'=>$model->getSequence(),
                'name'=>'landunit',
                'deletetoken'=>$this->csrfToken
            ]);
        }
        $links['cave']= $this->router->generate('dashboard_cave_edit', ['id'=>$model->getCave()->getId()]);
        return $links;
    }


    /**
     * Get the id.
     *
     * @param Cavelandunit $model
     *
     * @return int
     */
    public function getId($model): int
    {
        return $model->getSequence();
    }

    /**
     * Relationship cave
     * @param Cavelandunit $model
     * @return Relationship
     */
    public function cave(Cavelandunit $model): ?Relationship
    {
        return new Relationship(new Resource($model->getCave(), new CaveSerializer($this->router)));
    }
}