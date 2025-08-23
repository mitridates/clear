<?php

namespace App\Map\Infrastructure\Cache;

use App\Map\Domain\Entity\Map\Map;
use App\Shared\Infrastructure\Cache\FilesCache;
use App\Shared\tobscure\jsonapi\Document;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\Cache\ItemInterface;

class MapSerializedCache extends FilesCache
{
    public function __construct(#[Autowire('%kernel.project_dir%')] private readonly string $projectDir, #[Autowire('%kernel.environment%')] private readonly string $env)
    {
        parent::__construct($this->projectDir, $this->env);
    }

    const PREFIX= 'map.id.';

    public function updateSerializedMap(Map $map, Document $document):array
    {
        $prefix= self::PREFIX.$map->getId();
        $this->cache->clear($prefix);
        return $this->cache->get($prefix, function(ItemInterface $item) use ($document, $prefix)
        {
            $item->tag($prefix);//same tag as prefix to all relationship allow to remove all items related.
            return $document->toArray();
        });
    }

    /**
     * @param string $id
     * @return array
     */
    public function getSerializedMap(string $id): array
    {
        $data= [];
        try {
            $item = $this->cache->getItem(self::PREFIX.$id);
            $data= $item->isHit()? $item->get() : [];
        }catch (\Exception|InvalidArgumentException $e){}
        return $data;
    }

}