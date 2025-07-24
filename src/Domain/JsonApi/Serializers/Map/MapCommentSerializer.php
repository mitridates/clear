<?php

namespace App\Domain\JsonApi\Serializers\Map;
use App\Entity\Map\Mapcomment;
use App\Shared\reflection\EntityReflectionHelper;
use App\Shared\tobscure\jsonapi\AbstractSerializer;
use App\Shared\tobscure\jsonapi\Relationship;
use App\Shared\tobscure\jsonapi\Resource;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class MapCommentSerializer extends AbstractSerializer
{
    protected $type = 'mapcomment';

    public function __construct(readonly ?UrlGeneratorInterface $router= null, readonly ?CsrfTokenManagerInterface $tokenManager= null, readonly ?string $locale=null)
    {
    }

    /**
     * @param Mapcomment $model
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
     * @param Mapcomment $model
     * @return array
     */
    public function getLinks($model): array
    {
        if(!$this->router) return [];
        $links=[];
        $links['self']= $this->router->generate('admin_map_oto_edit',  [
            'id'=>$model->getMap()->getId(),
            'relationship'=>'comment',
        ]);
        if($this->tokenManager){
            $links['DELETE']= $this->router->generate('admin_map_oto_delete', [
                'id'=>$model->getMap()->getId(),
                'relationship'=>'comment',
                '_token'=>$this->tokenManager->getToken('delete_oto_comment_'.$model->getMap()->getId())
            ]);
        }
        $links['map']= $this->router->generate('admin_map_edit', ['id'=>$model->getMap()->getId()]);

        return $links;
    }

    /**
     * Relationship map
     * @param Mapcomment $model
     * @return ?Relationship
     */
    public function map(Mapcomment $model): ?Relationship
    {
        return new Relationship(new Resource($model->getMap(), new MapSerializer($this->router)));
    }
}