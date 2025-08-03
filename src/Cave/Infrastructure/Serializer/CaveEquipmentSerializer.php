<?php

namespace App\Cave\Infrastructure\Serializer;
use App\Cave\Domain\Entity\Caveequipment;
use App\Fielddefinition\Infrastructure\Serializer\FieldValueCodeSerializer;
use App\Shared\reflection\EntityReflectionHelper;
use App\Shared\tobscure\jsonapi\AbstractSerializer;
use App\Shared\tobscure\jsonapi\Relationship;
use App\Shared\tobscure\jsonapi\Resource;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CaveEquipmentSerializer extends AbstractSerializer
{

    protected $type = 'caveequipment';

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
     * @param Caveequipment $model
     * @param ?array $fields
     * @return array
     * @throws \ReflectionException
     */
    public function getAttributes($model, array $fields = null): array
    {
        $data= EntityReflectionHelper::serializeClassProperties($model, $fields);
        $data['cave']= $model->getCave()->getId();
        $data['priority']= $model->getPriority()->getId();
        return $data;
    }


    /**
     * @param Caveequipment $model
     * @return array
     */
    public function getLinks($model): array
    {
        if(!$this->router) return [];
        $links=[];
        $links['self']= $this->router->generate('dashboard_cave_updatemanytoone',  [
            'cave'=>$model->getCave()->getId(),
            'name'=>'equipment',
            'sequence'=>$model->getSequence()
        ]);
        if($this->csrfToken){

            $links['DELETE']= $this->router->generate('dashboard_cave_deletemanytoone', [
                'cave'=>$model->getCave()->getId(),
                'sequence'=>$model->getSequence(),
                'name'=>'equipment',
                'deletetoken'=>$this->csrfToken
            ]);
        }
        $links['cave']= $this->router->generate('dashboard_cave_edit', ['id'=>$model->getCave()->getId()]);
        return $links;
    }


    /**
     * Get the id.
     *
     * @param Caveequipment $model
     *
     * @return int
     */
    public function getId($model): int
    {
        return $model->getSequence();
    }

    /**
     * Relationship cave
     * @param Caveequipment $model
     * @return Relationship
     */
    public function cave(Caveequipment $model): ?Relationship
    {
        return new Relationship(new Resource($model->getCave(), new CaveSerializer($this->router)));
    }
    /**
     * Relationship Fieldvaluecode
     * @param Caveequipment $model
     * @return Relationship
     */
    public function priority(Caveequipment $model): ?Relationship
    {
        return new Relationship(new Resource($model->getPriority(), new FieldValueCodeSerializer($this->locale)));
    }
}