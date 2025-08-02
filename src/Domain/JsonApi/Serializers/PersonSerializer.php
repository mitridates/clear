<?php

namespace App\Domain\JsonApi\Serializers;
use App\Domain\JsonApi\Serializers\Geonames\Admin1Serializer;
use App\Domain\JsonApi\Serializers\Geonames\Admin2Serializer;
use App\Domain\JsonApi\Serializers\Geonames\Admin3Serializer;
use App\Domain\JsonApi\Serializers\Geonames\CountrySerializer;
use App\Domain\Person\Entity\Person;
use App\Shared\reflection\EntityReflectionHelper;
use App\Shared\tobscure\jsonapi\AbstractSerializer;
use App\Shared\tobscure\jsonapi\Relationship;
use App\Shared\tobscure\jsonapi\Resource;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PersonSerializer extends AbstractSerializer
{

    protected $type = 'person';
    private ?array $fields;
    public function __construct(protected readonly ?UrlGeneratorInterface $router= null)
    {
    }

    /**
     * @param Person $model
     * @param ?array $fields
     * @return array
     */
    public function getAttributes($model, array $fields = null): array
    {
        $this->fields= $fields;
        $data= EntityReflectionHelper::serializeClassProperties($model, $fields);
        $identity= ['organisation', 'country', 'admin1', 'admin2', 'admin3'];
        $intersect= $fields?  array_intersect($fields ,$identity) : $identity;
        if(!$fields || !empty($intersect)){
            foreach ($intersect as $v){
                $fn='get'.ucfirst(strtolower($v));
                $val= $model->$fn()? $model->$fn()->getName() : null;
                if($val){
                    $data[$v]=$val;
                }
            }
        }
        return $data;
    }


    /**
     * Relationship country
     */
    public function country(Person $model): ?Relationship
    {
        return $model->getCountry()? new Relationship(new Resource($model->getCountry(), new CountrySerializer())) : null;
    }
    /**
     * Relationship Admin1
     */
    public function admin1(Person $model): ?Relationship
    {
        return $model->getAdmin1()? new Relationship(new Resource($model->getAdmin1(), new Admin1Serializer())) : null;
    }

    /**
     * Relationship Admin2
     */
    public function admin2(Person $model): ?Relationship
    {
        return $model->getAdmin2()? new Relationship(new Resource($model->getAdmin2(), new Admin2Serializer())) : null;
    }

    /**
     * Relationship Admin3
     */
    public function admin3(Person $model): ?Relationship
    {
        return $model->getAdmin3()? new Relationship(new Resource($model->getAdmin3(), new Admin3Serializer())) : null;
    }

    /**
     * Relationship Organisation
     */
    public function organisation(Person $model): ?Relationship
    {
        return $model->getOrganisation()? new Relationship(new Resource($model->getOrganisation(), new OrganisationSerializer($this->router))) : null;
    }

    /**
     * Relationship Organisation
     */
    public function organisation2(Person $model): ?Relationship
    {
        return $model->getOrganisation2()? new Relationship(new Resource($model->getOrganisation2(), new OrganisationSerializer($this->router))) : null;
    }

    /**
     * Relationship Organisation
     */
    public function organisation3(Person $model): ?Relationship
    {
        return $model->getOrganisation3()? new Relationship(new Resource($model->getOrganisation3(), new OrganisationSerializer($this->router))) : null;
    }
    
    /**
     * @param Person $model
     * @return array
     */
    public function getLinks($model): array
    {
        if(!$this->router) return [];
        $links['self']= $this->router->generate('admin_person_edit', ['id'=>$model->getId()]);
        $identity= ['organisation', 'organisation2', 'organisation3'];
        $intersect= $this->fields?  array_intersect($this->fields ,$identity) : $identity;

        if(!$this->fields || !empty($intersect))
            foreach ($intersect as $v){
                $fn='get'.ucfirst(strtolower($v));
                if($model->$fn()){
                    $links[$v]= $this->router->generate('admin_organisation_edit', ['id'=>$model->$fn()->getId()]) ;
                }
            }
        return $links;
    }

}