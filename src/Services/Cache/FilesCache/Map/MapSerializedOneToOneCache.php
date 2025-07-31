<?php

namespace App\Services\Cache\FilesCache\Map;

use App\Domain\Map\Entity\Map\Map;
use App\Domain\Map\Entity\Map\Model\MapOneToOneInterface;
use App\Services\Cache\FilesCache;
use App\Shared\tobscure\jsonapi\AbstractSerializer;
use App\Shared\tobscure\jsonapi\Document;
use App\Shared\tobscure\jsonapi\Resource;
use App\Utils\Helper\MapControllerHelper;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Cache\ItemInterface;

class MapSerializedOneToOneCache extends FilesCache
{
    const PREFIX= 'map.oto.id.%s,%s';
    public function __construct(#[Autowire('%kernel.project_dir%')] private readonly string $projectDir, #[Autowire('%kernel.environment%')] private readonly string $env)
    {
        parent::__construct($this->projectDir, $this->env);
    }

    /**
     * @param Map $map
     * @param ?MapOneToOneInterface $data
     * @param string $relationship
     * @param UrlGeneratorInterface $urlGenerator
     * @return array
     * @throws InvalidArgumentException
     */
    public function updateOneToOneRelationship(Map $map, ?MapOneToOneInterface $data, string $relationship , UrlGeneratorInterface $urlGenerator):array
    {
        $prefix= sprintf(self::PREFIX, $map->getId(), $relationship);
        $mapPrefix= sprintf(MapSerializedCache::PREFIX, $map->getId());
        $this->cache->clear($prefix);
        if(!$data) return [];
        $serializerClass= MapControllerHelper::OTO_SERIALIZER[$relationship];

        /*******SERIALIZE MAP*******/
        /** @var AbstractSerializer $class*/
        $serializer= new $serializerClass($urlGenerator);
        $resource = new Resource($data, $serializer);
        $resource->with(
            MapControllerHelper::OTO_SERIALIZER_FIELDS[$relationship]['with']
        )
            ->fields(
                MapControllerHelper::OTO_SERIALIZER_FIELDS[$relationship]['fields']
            );

        $document= new Document($resource);

        return $this->cache->get($prefix, function(ItemInterface $item) use ($document, $mapPrefix){
            $item->tag($mapPrefix);//same tag as prefix to all relationship allow to remove all items related.
            return $document->toArray();
        });
    }

    /**
     * @param string $id
     * @param string $relationship
     * @return JsonResponse
     * @throws InvalidArgumentException
     */
    public function getOneToOneRelationhip(string $id, string $relationship): JsonResponse
    {
        $prefix= sprintf(self::PREFIX, $id, $relationship);
        $item = $this->cache->getItem($prefix);
        $data= $item->isHit()? $item->get():[];
        return new JsonResponse($data , 200, ['Content-Type'=>Document::MEDIA_TYPE]);

    }

}