<?php

namespace App\Services\Cache\FilesCache\Map;

use App\Entity\Map\Map;
use App\Entity\Map\Model\MapManyToOneInterface;
use App\Services\Cache\FilesCache;
use App\Utils\Helper\MapControllerHelper;
use App\Utils\Json\Serializers\Map\MapSerializer;
use App\vendor\tobscure\jsonapi\AbstractSerializer;
use App\vendor\tobscure\jsonapi\Collection;
use App\vendor\tobscure\jsonapi\Document;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Cache\ItemInterface;

class MapSerializedManyToOneCache extends FilesCache
{
    const PREFIX= 'map.mto.id.%s,%s';
    public function __construct(#[Autowire('%kernel.project_dir%')] private readonly string $projectDir, #[Autowire('%kernel.environment%')] private readonly string $env)
    {
        parent::__construct($this->projectDir, $this->env);
    }

    /**
     * @param Map $map
     * @param MapManyToOneInterface[] $data
     * @param string $relationship
     * @param UrlGeneratorInterface $urlGenerator
     * @return array
     * @throws InvalidArgumentException
     */
    public function updateManyToOneRelationship(Map $map, array $data, string $relationship , UrlGeneratorInterface $urlGenerator):array
    {
        $prefix= sprintf(self::PREFIX, $map->getId(), $relationship);
        $this->cache->clear($prefix);
        if(!count($data)) return [];

        $serializerClass= MapControllerHelper::MTO_SERIALIZER[$relationship];
        /*******SERIALIZE MAP*******/
        /** @var AbstractSerializer $class*/
        $serializer= new $serializerClass($urlGenerator);
        $collection = (new Collection($data, $serializer));
        $collection->with(
            MapControllerHelper::MTO_SERIALIZER_FIELDS[$relationship]['with']
        )
            ->fields(
                MapControllerHelper::MTO_SERIALIZER_FIELDS[$relationship]['fields']
            );

        return $this->cache->get($prefix, function(ItemInterface $item) use ($collection, $prefix){
            $item->tag($prefix);//same tag as prefix to all relationship allow to remove all items related.
            //return new JsonResponse($document->toArray() , 200, ['Content-Type'=>Document::MEDIA_TYPE]);
            return $collection->toArray();
        });
    }

    /**
     * @param string $id
     * @param string $relationship
     * @return JsonResponse
     * @throws InvalidArgumentException
     */
    public function getMap(string $id, string $relationship): JsonResponse
    {
        $prefix= sprintf(self::PREFIX, $id, $relationship);

        $item = $this->cache->getItem($prefix);
        $data= $item->isHit()? $item->get():[];
        return new JsonResponse($data , 200, ['Content-Type'=>Document::MEDIA_TYPE]);

    }

}