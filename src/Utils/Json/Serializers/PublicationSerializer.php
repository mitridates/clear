<?php

namespace App\Utils\Json\Serializers;
use App\Entity\Cavern\Publication;
use App\Utils\reflection\EntityReflectionHelper;
use App\Utils\Json\Serializers\Geonames\Admin1Serializer;
use App\Utils\Json\Serializers\Geonames\CountrySerializer;
use App\vendor\tobscure\jsonapi\AbstractSerializer;
use App\vendor\tobscure\jsonapi\Relationship;
use App\vendor\tobscure\jsonapi\Resource;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PublicationSerializer extends AbstractSerializer
{

    protected $type = 'publication';
    private ?array $fields;
    public function __construct(protected readonly ?UrlGeneratorInterface $router= null)
    {
    }

    /**
     * @param Publication $model
     * @param ?array $fields
     * @return array
     */
    public function getAttributes($model, ?array $fields = null): array
    {
        $data= EntityReflectionHelper::serializeClassProperties($model, $fields);
        $identity= ['country', 'admin1'];
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
     * Relationship country
     */
    public function country(Publication $model): ?Relationship
    {
        return $model->getCountry()? new Relationship(new Resource($model->getCountry(), new CountrySerializer())) : null;
    }
    /**
     * Relationship Admin1
     */
    public function admin1(Publication $model): ?Relationship
    {
        return $model->getAdmin1()? new Relationship(new Resource($model->getAdmin1(), new Admin1Serializer())) : null;
    }

    public function getLinks($model): array
    {
        if(!$this->router) return [];
        return ['self'=>$this->router->generate('admin_publication_edit', ['id'=>$model->getId()])];
    }
}