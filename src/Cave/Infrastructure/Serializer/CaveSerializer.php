<?php

namespace App\Cave\Infrastructure\Serializer;
use App\Area\Infrastructure\Serializer\AreaSerializer;
use App\Entity\Cavern\Trait;
use App\Fielddefinition\Infrastructure\Serializer\FieldValueCodeSerializer;
use App\Geonames\Infrastructure\Serializer\Admin1Serializer;
use App\Geonames\Infrastructure\Serializer\Admin2Serializer;
use App\Geonames\Infrastructure\Serializer\Admin3Serializer;
use App\Geonames\Infrastructure\Serializer\CountrySerializer;
use App\Shared\reflection\EntityReflectionHelper;
use App\Shared\tobscure\jsonapi\AbstractSerializer;
use App\Shared\tobscure\jsonapi\Relationship;
use App\Shared\tobscure\jsonapi\Resource;
use App\Utils\Json\Serializers\cave\Cave;
use App\Utils\Json\Serializers\cave\CaveDimensionSerializer;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CaveSerializer extends AbstractSerializer
{

    protected $type = 'cave';

    protected ?UrlGeneratorInterface $router;

    protected ?string $locale;

    protected array $fields;

    public function __construct($router= null, $locale=null)
    {
        $this->router = $router;
        $this->locale= $locale;
    }

    /**
     * @param Cave $model
     * @param ?array $fields
     * @return array
     */
    public function getAttributes($model, array $fields = null): array
    {
        $data= EntityReflectionHelper::serializeClassProperties($model, $fields);
        $identity= ['organisation', 'country', 'admin1', 'admin2', 'admin3', 'area', 'caveposition', 'cavedimension', 'lengthcategory', 'depthcategory'];
        $intersect= $fields?  array_intersect($fields ,$identity) : $identity;

        if(!$fields || !empty($intersect)){
            foreach ($intersect as $v){
                $fn='get'.ucfirst(strtolower($v));
                if(in_array($v, ['caveposition', 'cavedimension'])){
                    $val= $model->$fn()? $model->$fn()->getCave()->getId() : null;
                }else{
                    $val= $model->$fn()? $model->$fn()->getId() : null;
                }

                if($val){
                    $data[$v]=$val;
                }
            }
        }

        if(in_array('names', $fields)){
            $data['names'][]=$model->getName();
            if($model->getCavename()->count()){
                foreach ($model->getCavename()->getIterator() as $i=>$cname){
                    $data['names'][]= $cname->getName();
                }
            }
        }

        return $data;
    }

    /**
     * Relationship Country
     */
    public function country(Cave $model): ?Relationship
    {
        return $model->getCountry()? new Relationship(new Resource($model->getCountry(), new CountrySerializer())) : null;
    }

    /**
     * Relationship Area
     */
    public function area(Cave $model): ?Relationship
    {
        return $model->getArea()? new Relationship(new Resource($model->getArea(), new AreaSerializer($this->router))) : null;
    }

    /**
     * Relationship Admin1
     */
    public function admin1(Cave $model): ?Relationship
    {
        return $model->getAdmin1()? new Relationship(new Resource($model->getAdmin1(), new Admin1Serializer())) : null;
    }

    /**
     * Relationship Admin2
     */
    public function admin2(Cave $model): ?Relationship
    {
        return $model->getAdmin2()? new Relationship(new Resource($model->getAdmin2(), new Admin2Serializer())) : null;
    }

    /**
     * Relationship Admin3
     */
    public function admin3(Cave $model): ?Relationship
    {
        return $model->getAdmin3()? new Relationship(new Resource($model->getAdmin3(), new Admin3Serializer())) : null;
    }

    /**
     * Relationship Fieldvaluecode
     * @param Cave $model
     * @return Relationship
     */
    public function lengthcategory(Cave $model): ?Relationship
    {
        return $model->getLengthcategory()? new Relationship(new Resource($model->getLengthcategory(), new FieldValueCodeSerializer($this->locale))) : null;
    }
    /**
     * Relationship Fieldvaluecode
     * @param Cave $model
     * @return Relationship
     */
    public function depthcategory(Cave $model): ?Relationship
    {
        return $model->getDepthcategory()? new Relationship(new Resource($model->getDepthcategory(), new FieldValueCodeSerializer($this->locale))) : null;
    }
    /**
     * Relationship Fieldvaluecode
     */
    public function caveposition(Cave $model): ?Relationship
    {
        return $model->getCaveposition()? new Relationship(new Resource($model->getCaveposition(), new CavePositionSerializer($this->router))) : null;
    }
    /**
     * Relationship Fieldvaluecode
     */
    public function cavedimension(Cave $model): ?Relationship
    {
        return $model->getCavedimension()? new Relationship(new Resource($model->getCavedimension(), new CaveDimensionSerializer($this->router))) : null;
    }

    public function getLinks($model): array
    {
        if(!$this->router) return [];
        return ['self'=>$this->router->generate('dashboard_cave_edit', ['id'=>$model->getId()])];
    }
}