<?php

namespace App\Cave\Infrastructure\Serializer;
use App\Cave\Domain\Entity\Cavediscovery;
use App\Fielddefinition\Infrastructure\Serializer\FieldValueCodeSerializer;
use App\Organisation\Infrastructure\Serializer\OrganisationSerializer;
use App\Person\Infrastructure\Serializer\PersonSerializer;
use App\Shared\reflection\EntityReflectionHelper;
use App\Shared\tobscure\jsonapi\AbstractSerializer;
use App\Shared\tobscure\jsonapi\Relationship;
use App\Shared\tobscure\jsonapi\Resource;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CaveDiscoverySerializer extends AbstractSerializer
{

    protected $type = 'cavediscovery';

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
     * @param Cavediscovery $model
     * @param ?array $fields
     * @return array
     */
    public function getAttributes($model, array $fields = null): array
    {
        $data= EntityReflectionHelper::serializeClassProperties($model, $fields);
        $identity= ['cave', 'type', 'datequalifier', 'organisation', 'person'];
        $intersect= $fields?  array_intersect($fields ,$identity) : $identity;

        if(!$fields || !empty($intersect))
            foreach ($intersect as $v){
                $fn='get'.ucfirst(strtolower($v));
                $val= $model->$fn()? $model->$fn()->getId() : null;
                if($val){
                    $data[$v]=$val;
                }
            }
        return $data;
    }


    /**
     * @param Cavediscovery $model
     * @return array
     */
    public function getLinks($model): array
    {
        if(!$this->router) return [];
        $links=[];
        $links['self']= $this->router->generate('dashboard_cave_updatemanytoone',  [
            'cave'=>$model->getCave()->getId(),
            'name'=>'discovery',
            'sequence'=>$model->getSequence()
        ]);
        if($this->csrfToken){

            $links['DELETE']= $this->router->generate('dashboard_cave_deletemanytoone', [
                'cave'=>$model->getCave()->getId(),
                'sequence'=>$model->getSequence(),
                'name'=>'discovery',
                'deletetoken'=>$this->csrfToken
            ]);
        }
        $links['cave']= $this->router->generate('dashboard_cave_edit', ['id'=>$model->getCave()->getId()]);
        return $links;
    }


    /**
     * Get the id.
     *
     * @param Cavediscovery $model
     *
     * @return int
     */
    public function getId($model): int
    {
        return $model->getSequence();
    }

    /**
     * Relationship cave
     * @param Cavediscovery $model
     * @return Relationship
     */
    public function cave(Cavediscovery $model): ?Relationship
    {
        return new Relationship(new Resource($model->getCave(), new CaveSerializer($this->router)));
    }

    /**
     * Relationship person
     * @param Cavediscovery $model
     * @return Relationship
     */
    public function person(Cavediscovery $model): ?Relationship
    {
        if(!$model->getPerson()) return null;
        return new Relationship(new Resource($model->getPerson(), new PersonSerializer($this->router)));
    }

    /**
     * Relationship organisation
     * @param Cavediscovery $model
     * @return Relationship
     */
    public function organisation(Cavediscovery $model): ?Relationship
    {
        if(!$model->getOrganisation()) return null;
        return new Relationship(new Resource($model->getOrganisation(), new OrganisationSerializer($this->router)));
    }

    /**
     * Relationship Fieldvaluecode
     * @param Cavediscovery $model
     * @return Relationship|null
     */
    public function type(Cavediscovery $model): ?Relationship
    {
        if(!$model->getType()) return null;
        return new Relationship(new Resource($model->getType(), new FieldValueCodeSerializer($this->locale)));
    }

    /**
     * Relationship Fieldvaluecode
     * @param Cavediscovery $model
     * @return Relationship
     */
    public function datequalifier(Cavediscovery $model): ?Relationship
    {
        if(!$model->getDatequalifier()) return null;
        return new Relationship(new Resource($model->getDatequalifier(), new FieldValueCodeSerializer($this->locale)));
    }
}