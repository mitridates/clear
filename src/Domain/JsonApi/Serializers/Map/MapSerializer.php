<?php

namespace App\Domain\JsonApi\Serializers\Map;
use App\Domain\JsonApi\Serializers\FieldValueCodeSerializer;
use App\Domain\JsonApi\Serializers\Geonames\Admin1Serializer;
use App\Domain\JsonApi\Serializers\Geonames\Admin2Serializer;
use App\Domain\JsonApi\Serializers\Geonames\Admin3Serializer;
use App\Domain\JsonApi\Serializers\Geonames\CountrySerializer;
use App\Domain\JsonApi\Serializers\OrganisationSerializer;
use App\Domain\JsonApi\Serializers\PersonSerializer;
use App\Entity\Map\Map;
use App\Shared\reflection\EntityReflectionHelper;
use App\Shared\tobscure\jsonapi\AbstractSerializer;
use App\Shared\tobscure\jsonapi\Relationship;
use App\Shared\tobscure\jsonapi\Resource;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class MapSerializer extends AbstractSerializer
{
    protected $type = 'map';
    protected array $fields;

    public function __construct(protected  ?UrlGeneratorInterface $router= null)
    {

    }

    /**
     * @param Map $model
     * @param ?array $fields
     * @return array
     */
    public function getAttributes($model, array $fields = null): array
    {
        $data= EntityReflectionHelper::serializeClassProperties($model, $fields);
        $identity= ['sourcecountry','sourceorg', 'country', 'admin1', 'admin2', 'admin3', 'principalsurveyorid', 'principaldrafterid'];
        $partialCoordinates= [];
        $intersect= $fields?  array_intersect($fields ,$identity) : $identity;

        if(!$fields || !empty($intersect))
        {
            foreach ($intersect as $v){
                $fn='get'.ucfirst(strtolower($v));
                $val= $model->$fn()? $model->$fn()->getId() : null;
                if($val){
                    $data[$v]=$val;
                }
            }
        }
        return $data;
    }

    /**
     * Relationship Person
     * @param Map $model
     * @return Relationship|null
     */
    public function principalsurveyorid(Map $model): ?Relationship
    {
        return $model->getPrincipalsurveyorid()? new Relationship(new Resource($model->getPrincipalsurveyorid(), new PersonSerializer($this->router))) : null;
    }
    /**
     * Relationship Person
     * @param Map $model
     * @return Relationship|null
     */
    public function principaldrafterid(Map $model): ?Relationship
    {
        return $model->getPrincipaldrafterid()? new Relationship(new Resource($model->getPrincipaldrafterid(), new PersonSerializer($this->router))) : null;
    }

    /**
     * Relationship country
     * @param Map $model
     * @return Relationship|null
     */
    public function country(Map $model): ?Relationship
    {
         return $model->getCountry()? new Relationship(new Resource($model->getCountry(), new CountrySerializer())) : null;
    }

    /**
     * Relationship country
     * @param Map $model
     * @return Relationship|null
     */
    public function sourcecountry(Map $model): ?Relationship
    {
        return $model->getSourcecountry()? new Relationship(new Resource($model->getSourcecountry(), new CountrySerializer())) : null;
    }

    /**
     * Relationship Organisation
     */
    public function sourceorg(Map $model): ?Relationship
    {
        return $model->getSourceorg()? new Relationship(new Resource($model->getSourceorg(), new OrganisationSerializer($this->router))) : null;
    }
    /**
     * Relationship Admin1
     * @param Map $model
     * @return Relationship|null
     */
    public function admin1(Map $model): ?Relationship
    {
        return $model->getAdmin1()? new Relationship(new Resource($model->getAdmin1(), new Admin1Serializer())) : null;
    }

    /**
     * Relationship Admin2
     * @param Map $model
     * @return Relationship|null
     */
    public function admin2(Map $model): ?Relationship
    {
        return $model->getAdmin2()? new Relationship(new Resource($model->getAdmin2(), new Admin2Serializer())) : null;
    }

    /**
     * Relationship Admin3
     * @param Map $model
     * @return Relationship|null
     */
    public function admin3(Map $model): ?Relationship
    {
        return $model->getAdmin3()? new Relationship(new Resource($model->getAdmin3(), new Admin3Serializer())) : null;
    }

    /**
     * Relationship Fieldvaluecode
     * @param Map $model
     * @return Relationship|null
     */
    public function type(Map $model): ?Relationship
    {
        return $model->getType()? new Relationship(new Resource($model->getType(), new FieldValueCodeSerializer())) : null;
    }

    /**
     * Relationship Fieldvaluecode
     * @param Map $model
     * @return Relationship|null
     */
    public function sourcetype(Map $model): ?Relationship
    {
        return $model->getSourcetype()? new Relationship(new Resource($model->getSourcetype(), new FieldValueCodeSerializer())) : null;
    }

    public function getLinks($model): array
    {
        if(!$this->router) return [];
        $links= ['self'=>$this->router->generate('admin_map_edit', ['id'=>$model->getId()])];
        $links['view']=$this->router->generate('admin_map_view', ['id'=>$model->getId()]);
        return $links;
    }
}