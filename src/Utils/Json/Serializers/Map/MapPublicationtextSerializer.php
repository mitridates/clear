<?php

namespace App\Utils\Json\Serializers\Map;
use App\Entity\Map\Mappublicationtext;
use App\Utils\reflection\EntityReflectionHelper;
use App\vendor\tobscure\jsonapi\AbstractSerializer;
use App\vendor\tobscure\jsonapi\Relationship;
use App\vendor\tobscure\jsonapi\Resource;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class MapPublicationtextSerializer extends AbstractSerializer
{
    protected $type = 'mappublicationtext';

    public function __construct(readonly ?UrlGeneratorInterface $router= null, readonly ?CsrfTokenManagerInterface $tokenManager= null, readonly ?string $locale=null)
    {
    }

    /**
     * @param Mappublicationtext $model
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
     * @param Mappublicationtext $model
     * @return array
     */
    public function getLinks($model): array
    {
        if(!$this->router) return [];
        $links=[];
        $links['self']= $this->router->generate('admin_map_oto_edit',  [
            'id'=>$model->getMap()->getId(),
            'relationship'=>'publicationtext',
        ]);
        if($this->tokenManager){
            $links['DELETE']= $this->router->generate('admin_map_oto_delete', [
                'id'=>$model->getMap()->getId(),
                'relationship'=>'publicationtext',
                '_token'=>$this->tokenManager->getToken('delete_oto_publicationtext_'.$model->getMap()->getId())
            ]);
        }
        $links['map']= $this->router->generate('admin_map_edit', ['id'=>$model->getMap()->getId()]);

        return $links;
    }

    /**
     * Relationship map
     * @param Mappublicationtext $model
     * @return ?Relationship
     */
    public function map(Mappublicationtext $model): ?Relationship
    {
        return new Relationship(new Resource($model->getMap(), new MapSerializer($this->router)));
    }
}