<?php

namespace App\Cave\Infrastructure\Serializer;
use App\Area\Infrastructure\Serializer\AreaSerializer;
use App\Cave\Domain\Entity\Cavecrossreference;
use App\Geonames\Infrastructure\Serializer\Admin1Serializer;
use App\Geonames\Infrastructure\Serializer\CountrySerializer;
use App\Shared\reflection\EntityReflectionHelper;
use App\Shared\tobscure\jsonapi\AbstractSerializer;
use App\Shared\tobscure\jsonapi\Relationship;
use App\Shared\tobscure\jsonapi\Resource;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CaveCrossreferenceSerializer extends AbstractSerializer
{

    protected $type = 'cavecrossreference';

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
     * @param Cavecrossreference $model
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
     * @param Cavecrossreference $model
     * @return array
     */
    public function getLinks($model): array
    {
        if(!$this->router) return [];
        $links=[];
        $links['self']= $this->router->generate('dashboard_cave_updatemanytoone',  [
            'cave'=>$model->getCave()->getId(),
            'name'=>'crossreference',
            'sequence'=>$model->getSequence()
        ]);
        if($this->csrfToken){

            $links['DELETE']= $this->router->generate('dashboard_cave_deletemanytoone', [
                'cave'=>$model->getCave()->getId(),
                'sequence'=>$model->getSequence(),
                'name'=>'crossreference',
                'deletetoken'=>$this->csrfToken
            ]);
        }
        $links['cave']= $this->router->generate('dashboard_cave_edit', ['id'=>$model->getCave()->getId()]);
        return $links;
    }


    /**
     * Get the id.
     *
     * @param Cavecrossreference $model
     *
     * @return int
     */
    public function getId($model): int
    {
        return $model->getSequence();
    }


    /**
     * Relationship Country
     * @param Cavecrossreference $model
     * @return Relationship
     */
    public function country(Cavecrossreference $model): ?Relationship
    {
        return $model->getCountry()? new Relationship(new Resource($model->getCountry(), new CountrySerializer())) : null;
    }

    /**
     * Relationship Admin1
     * @param Cavecrossreference $model
     * @return Relationship
     */
    public function admin1(Cavecrossreference $model): ?Relationship
    {
        return $model->getAdmin1()? new Relationship(new Resource($model->getAdmin1(), new Admin1Serializer())) : null;
    }

    /**
     * Relationship Area
     * @param Cavecrossreference $model
     * @return Relationship
     */
    public function area(Cavecrossreference $model): ?Relationship
    {
        return $model->getArea()? new Relationship(new Resource($model->getArea(), new AreaSerializer($this->router))) : null;
    }

    /**
     * Relationship cave
     * @param Cavecrossreference $model
     * @return Relationship
     */
    public function cave(Cavecrossreference $model): ?Relationship
    {
        return new Relationship(new Resource($model->getCave(), new CaveSerializer($this->router)));
    }

}