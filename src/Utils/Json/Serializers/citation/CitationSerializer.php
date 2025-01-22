<?php

namespace App\Utils\Json\Serializers\citation;
use App\Entity\Cavern\Citation;
use App\Utils\reflection\EntityReflectionHelper;
use App\Utils\Json\Serializers\LinkSerializer;
use App\Utils\Json\Serializers\Geonames\Admin1Serializer;
use App\Utils\Json\Serializers\Geonames\CountrySerializer;
use App\vendor\tobscure\jsonapi\AbstractSerializer;
use App\vendor\tobscure\jsonapi\Relationship;
use App\vendor\tobscure\jsonapi\Resource;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CitationSerializer extends AbstractSerializer
{

    protected $type = 'citation';
    /**
     * @var UrlGeneratorInterface
     */
    protected $router;

    public function __construct($router= null)
    {
        $this->router = $router;
    }

    /**
     * @param Citation|array $model
     * @param array|null $fields
     * @return array
     */
    public function getAttributes($model, array $fields = null): array
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
     * Relationship caved
     * @param Citation $model
     * @return Relationship|null
     */
    public function link(Citation $model): ?Relationship
    {
        return $model->getLink()? new Relationship(new Resource($model->getLink(), new LinkSerializer($this->router))) : null;
    }

    /**
     * Relationship country
     * @param Citation $model
     * @return Relationship|null
     */
    public function country(Citation $model): ?Relationship
    {
        return $model->getCountry()? new Relationship(new Resource($model->getCountry(), new CountrySerializer())) : null;
    }

    /**
     * Relationship Admin1
     * @param Citation $model
     * @return Relationship|null
     */
    public function admin1(Citation $model): ?Relationship
    {
        return $model->getAdmin1()? new Relationship(new Resource($model->getAdmin1(), new Admin1Serializer())) : null;
    }
    public function getLinks($model): array
    {
        if(!$this->router) return [];
        return ['self'=>$this->router->generate('dashboard_citation_edit', ['id'=>$model->getId()])];
    }


}