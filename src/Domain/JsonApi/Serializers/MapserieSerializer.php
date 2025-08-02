<?php

namespace App\Domain\JsonApi\Serializers;
use App\Domain\Mapserie\Entity\Mapserie;
use App\Shared\reflection\EntityReflectionHelper;
use App\Shared\tobscure\jsonapi\AbstractSerializer;
use App\Shared\tobscure\jsonapi\Relationship;
use App\Shared\tobscure\jsonapi\Resource;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class MapserieSerializer extends AbstractSerializer
{

    protected $type = 'mapserie';
    public function __construct(protected readonly ?UrlGeneratorInterface $router= null)
    {
    }

    /**
     * @param Mapserie|array $model
     * @param ?array $fields
     * @return array
     */
    public function getAttributes($model, ?array $fields = null): array
    {
        $data= EntityReflectionHelper::serializeClassProperties($model, $fields);
        $identity= ['lengthunits', 'maptype', 'publisher'];
        $intersect= $fields?  array_intersect($fields ,$identity) : $identity;

        if(!$fields || !empty($intersect))
            foreach ($intersect as $v){
                $fn='get'.ucfirst(strtolower($v));

                if($v==='publisher')
                {
                    $val=  $model->$fn()? $model->$fn()->getId() : null;
                    if($val){
                        $data[$v]=$val;
                    }
                    continue;
                }

                $val=  $model->$fn()? $model->$fn()->getValue() : null;
                if($val){
                    $data[$v]=$val;
                }
            }

        return $data;
    }

    /**
     * Relationship country
     * @param Mapserie $model
     * @return ?Relationship
     */
    public function publisher(Mapserie $model): ?Relationship
    {
        return $model->getPublisher()? new Relationship(new Resource($model->getPublisher(), new OrganisationSerializer())) : null;
    }

    public function getLinks($model): array
    {
        $links=[];
        if($this->router){
            $links['self']= $this->router->generate('admin_mapserie_edit', ['id'=>$model->getId()]);
        }
        return $links;
    }
}