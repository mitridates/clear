<?php

namespace App\Organisation\Infrastructure\Serializer;
use App\Fielddefinition\Infrastructure\Serializer\FieldValueCodeSerializer;
use App\Geonames\Infrastructure\Serializer\Admin1Serializer;
use App\Geonames\Infrastructure\Serializer\Admin2Serializer;
use App\Geonames\Infrastructure\Serializer\Admin3Serializer;
use App\Geonames\Infrastructure\Serializer\CountrySerializer;
use App\Organisation\Domain\Entity\Organisation;
use App\Shared\reflection\EntityReflectionHelper;
use App\Shared\tobscure\jsonapi\AbstractSerializer;
use App\Shared\tobscure\jsonapi\Relationship;
use App\Shared\tobscure\jsonapi\Resource;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class OrganisationSerializer extends AbstractSerializer
{

    protected $type = 'organisation';
    protected ?array $fields;

    public function __construct(protected readonly ?UrlGeneratorInterface $router= null)
    {
    }

    /**
     * @param Organisation $model
     * @param ?array $fields
     * @return array
     */
    public function getAttributes($model, array $fields = null): array
    {
        $data= EntityReflectionHelper::serializeClassProperties($model, $fields);
        $identity= ['country', 'admin1', 'admin2', 'admin3'];
        $intersect= $fields?  array_intersect($fields ,$identity) : $identity;

        if(!$fields || !empty($intersect)){
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
     * Relationship country
     */
    public function country(Organisation $model): ?Relationship
    {
        return $model->getCountry()? new Relationship(new Resource($model->getCountry(), new CountrySerializer())) : null;
    }
    /**
     * Relationship Admin1
     */
    public function admin1(Organisation $model): ?Relationship
    {
        return $model->getAdmin1()? new Relationship(new Resource($model->getAdmin1(), new Admin1Serializer())) : null;
    }

    /**
     * Relationship Admin2
     */
    public function admin2(Organisation $model): ?Relationship
    {
        return $model->getAdmin2()? new Relationship(new Resource($model->getAdmin2(), new Admin2Serializer())) : null;
    }

    /**
     * Relationship Admin3
     */
    public function admin3(Organisation $model): ?Relationship
    {
        return $model->getAdmin3()? new Relationship(new Resource($model->getAdmin3(), new Admin3Serializer())) : null;
    }
    
    public function getLinks(mixed $model): array
    {
        if(!$this->router) return [];
        return ['self'=>$this->router->generate('admin_organisation_edit', ['id'=>$model->getId()])];
    }

    /**
     * Relationship type
     */
    public function type(Organisation $model): ?Relationship
    {
        return $model->getType()? new Relationship(new Resource($model->getType(), new FieldValueCodeSerializer())) : null;
    }

}