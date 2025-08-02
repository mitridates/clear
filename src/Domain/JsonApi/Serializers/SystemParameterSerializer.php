<?php

namespace App\Domain\JsonApi\Serializers;
use App\Domain\JsonApi\Serializers\Geonames\CountrySerializer;
use App\Domain\SystemParameter\Entity\SystemParameter;
use App\Entity\Cavern\Sysparam;
use App\Shared\reflection\EntityReflectionHelper;
use App\Shared\tobscure\jsonapi\AbstractSerializer;
use App\Shared\tobscure\jsonapi\Relationship;
use App\Shared\tobscure\jsonapi\Resource;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class SystemParameterSerializer extends AbstractSerializer
{

    protected $type = 'sysparam';
    protected array $fields;

    public function __construct(protected readonly ?UrlGeneratorInterface $router= null)
    {
    }

    /**
     * @param SystemParameter $model
     * @param array|null $fields
     * @return array
     */
    public function getAttributes($model, ?array $fields = null): array
    {
        $data= EntityReflectionHelper::serializeClassProperties($model, $fields);
        $identity= ['country', 'organisationdbm', 'organisationsite', 'mapserie'];
        $intersect= $fields?  array_intersect($fields ,$identity) : $identity;

        if(!$fields || !empty($intersect))
            foreach ($intersect as $v){
                $fn='get'.ucfirst(strtolower($v));
                $val= $model->$fn()? $model->$fn()->getName() : null;
                if($val){
                    $data[$v]=$val;
                }
            }

        return $data;
    }

    /**
     * Relationship country
     * @param SystemParameter $model
     * @return ?Relationship
     */
    public function country(SystemParameter $model): ?Relationship
    {
        return $model->getCountry()? new Relationship(new Resource($model->getCountry(), new CountrySerializer())) : null;
    }

    /**
     * Relationship Organisation
     * @param SystemParameter $model
     * @return ?Relationship
     */
    public function organisation(SystemParameter $model): ?Relationship
    {
        return $model->getOrganisationdbm()? new Relationship(new Resource($model->getOrganisationdbm(), new OrganisationSerializer($this->router))) : null;
    }

    /**
     * Relationship Organisation
     * @param SystemParameter $model
     * @return ?Relationship
     */
    public function organisationsite(SystemParameter $model): ?Relationship
    {
        return $model->getOrganisationsite()? new Relationship(new Resource($model->getOrganisationsite(), new OrganisationSerializer($this->router))) : null;
    }

    /**
     * @param SystemParameter $model
     */
    public function getLinks($model): array
    {
        if(!$this->router) return [];
        $links['self']= $this->router->generate('admin_system_parameter_edit', ['id'=>$model->getId()]);
//        $identity= ['organisationdbm', 'organisationsite', 'mapserie'];
//        $intersect= $this->fields?  array_intersect($this->fields ,$identity) : $identity;
//
//        if(!$this->fields || !empty($intersect))
//            foreach ($intersect as $v){
//                $fn='get'.ucfirst(strtolower($v));
//                if(!$model->$fn()) continue;
//                if($v==='mapserie'){
//                    $links[$v]= $this->router->generate('dashboard_mapserie_edit', ['id'=>$model->$fn()->getId()]) ;
//                    continue;
//                }
//                if($model->$fn()){
//                    $links[$v]= $this->router->generate('dashboard_organisation_edit', ['id'=>$model->$fn()->getId()]) ;
//                }
//            }
        return $links;
    }
}