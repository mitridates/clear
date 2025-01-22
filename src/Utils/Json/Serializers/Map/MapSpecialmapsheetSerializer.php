<?php

namespace App\Utils\Json\Serializers\Map;
use App\Entity\Map\Mapspecialmapsheet;
use App\Utils\reflection\EntityReflectionHelper;
use App\vendor\tobscure\jsonapi\AbstractSerializer;
use App\vendor\tobscure\jsonapi\Relationship;
use App\vendor\tobscure\jsonapi\Resource;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class MapSpecialmapsheetSerializer extends AbstractSerializer
{
    protected $type = 'mapspecialmapsheet';

    public function __construct(readonly ?UrlGeneratorInterface $router= null, readonly ?CsrfTokenManagerInterface $tokenManager= null, readonly ?string $locale=null)
    {
    }

    /**
     * @param Mapspecialmapsheet $model
     * @param ?array $fields
     * @return array
     */
    public function getAttributes($model, array $fields = null): array
    {
        $data= EntityReflectionHelper::serializeClassProperties($model, $fields);
        $data['map']= $model->getMap()->getId();
        return $data;
    }

    /**
     * @param Mapspecialmapsheet $model
     * @return array
     */
    public function getLinks($model): array
    {
        if(!$this->router) return [];
        $links=[];
        $links['self']= $this->router->generate('admin_map_oto_edit',  [
            'id'=>$model->getMap()->getId(),
            'relationship'=>'specialmapsheet',
        ]);
        if($this->tokenManager){
            $links['DELETE']= $this->router->generate('admin_map_oto_delete', [
                'id'=>$model->getMap()->getId(),
                'relationship'=>'specialmapsheet',
                '_token'=>$this->tokenManager->getToken('delete_oto_comment_'.$model->getMap()->getId())
            ]);
        }
        $links['map']= $this->router->generate('admin_map_edit', ['id'=>$model->getMap()->getId()]);

        return $links;
    }

    /**
     * Relationship map
     * @param Mapspecialmapsheet $model
     * @return ?Relationship
     */
    public function map(Mapspecialmapsheet $model): ?Relationship
    {
        return new Relationship(new Resource($model->getMap(), new MapSerializer($this->router)));
    }
}