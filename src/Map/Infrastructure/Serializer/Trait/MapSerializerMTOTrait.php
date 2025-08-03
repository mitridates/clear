<?php

namespace App\Map\Infrastructure\Serializer\Trait;

use App\Map\Domain\Entity\Map\Model\MapManyToOneInterface;
use App\Map\Infrastructure\Serializer\MapSerializer;
use App\Shared\tobscure\jsonapi\Relationship;
use App\Shared\tobscure\jsonapi\Resource;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

trait MapSerializerMTOTrait
{
    protected array $fields;

    public function __construct(
        protected  ?UrlGeneratorInterface $urlGenerator= null,
        protected ?CsrfTokenManagerInterface  $tokenManager= null,
        protected ?string $locale=null
    ){}

    /**
     * @param MapManyToOneInterface $model
     */
    public function getId($model): int|string
    {
        return $model->getSequence();
    }

    public function getManyToOneLinks(MapManyToOneInterface $model, string $relationship): array
    {
        $links=[];
        if(!$this->urlGenerator) return $links;
        $args= [
            'id'=>$model->getMap()->getId(),
            'relationship'=>$relationship,
            'sequence'=>$model->getSequence()
        ];

        $links['get']=$links['self']= $this->urlGenerator->generate('admin_map_mto_update',
            array_merge($args, ['req'=>'get'])
        );

        $links['set']= $this->urlGenerator->generate('admin_map_mto_update',
            array_merge($args, ['req'=>'set'])
        );

        if($this->tokenManager){
            $withToken= array_merge($args,['_token'=>$this->tokenManager->getToken(
                $relationship.$model->getMap()->getId().$model->getSequence().'_delete_token'
            )]);
            $links['delete']= $this->urlGenerator->generate('admin_map_mto_delete', $withToken);
        }
        $links['map']= $this->urlGenerator->generate('admin_map_edit', ['id'=>$model->getMap()->getId()]);
//        $links['link']= $this->urlGenerator->generate('admin_link_edit', ['id'=>$model->getLink()->getId()]);
        return $links;
    }


    /**
     * Relationship map
     * @param MapManyToOneInterface $model
     * @return Relationship
     */
    public function map(mixed $model): Relationship
    {
        return new Relationship(new Resource($model->getMap(), new MapSerializer($this->urlGenerator)));
    }
}