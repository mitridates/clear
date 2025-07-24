<?php

namespace App\Domain\JsonApi\Serializers\cave;
use App\Domain\JsonApi\Serializers\AreaSerializer;
use App\Domain\JsonApi\Serializers\Geonames\Admin1Serializer;
use App\Domain\JsonApi\Serializers\Geonames\CountrySerializer;
use App\Entity\Cave\Cavepreviousnumber;
use App\Entity\Cavern\Trait;
use App\Shared\reflection\EntityReflectionHelper;
use App\Shared\tobscure\jsonapi\AbstractSerializer;
use App\Shared\tobscure\jsonapi\Relationship;
use App\Shared\tobscure\jsonapi\Resource;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CavePreviousnumberSerializer extends AbstractSerializer
{

    protected $type = 'cavepreviousnumber';

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
     * @param Cavepreviousnumber $model
     * @param ?array $fields
     * @return array
     */
    public function getAttributes($model, array $fields = null): array
    {
        $data= EntityReflectionHelper::serializeClassProperties($model, $fields);
        $identity= ['cave', 'country', 'admin1', 'area'];
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
     * @param Cavepreviousnumber $model
     * @return array
     */
    public function getLinks($model): array
    {
        if(!$this->router) return [];
        $links=[];
        $links['self']= $this->router->generate('dashboard_cave_updatemanytoone',  [
            'cave'=>$model->getCave()->getId(),
            'name'=>'previousnumber',
            'sequence'=>$model->getSequence()
        ]);
        if($this->csrfToken){

            $links['DELETE']= $this->router->generate('dashboard_cave_deletemanytoone', [
                'cave'=>$model->getCave()->getId(),
                'sequence'=>$model->getSequence(),
                'name'=>'previousnumber',
                'deletetoken'=>$this->csrfToken
            ]);
        }
        $links['cave']= $this->router->generate('dashboard_cave_edit', ['id'=>$model->getCave()->getId()]);
        return $links;
    }


    /**
     * Get the id.
     *
     * @param Cavepreviousnumber $model
     *
     * @return int
     */
    public function getId($model): int
    {
        return $model->getSequence();
    }


    /**
     * Relationship Country
     * @param Cavepreviousnumber $model
     * @return Relationship
     */
    public function country(Cavepreviousnumber $model): ?Relationship
    {
        return new Relationship(new Resource($model->getCountry(), new CountrySerializer()));
    }

    /**
     * Relationship Admin1
     * @param Cavepreviousnumber $model
     * @return Relationship
     */
    public function admin1(Cavepreviousnumber $model): ?Relationship
    {
        return new Relationship(new Resource($model->getAdmin1(), new Admin1Serializer()));
    }

    /**
     * Relationship Area
     * @param Cavepreviousnumber $model
     * @return Relationship
     */
    public function area(Cavepreviousnumber $model): ?Relationship
    {
        return new Relationship(new Resource($model->getArea(), new AreaSerializer($this->router)));
    }

    /**
     * Relationship cave
     * @param Cavepreviousnumber $model
     * @return Relationship
     */
    public function cave(Cavepreviousnumber $model): ?Relationship
    {
        return new Relationship(new Resource($model->getCave(), new CaveSerializer($this->router)));
    }

}