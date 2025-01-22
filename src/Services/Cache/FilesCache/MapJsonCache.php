<?php

namespace App\Services\Cache\FilesCache;

use App\Services\Cache\FilesCache;
use App\vendor\tobscure\jsonapi\Collection;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Cache\ItemInterface;

class MapJsonCache extends FilesCache
{
    const PREFIX = 'map.';
    const MAP_SUFFIX = [
        'cave',
        'citation',
        'comment,',
        'commentline',
        'controller',
        'details',
        'drafter',
        'furthergc',
        'furtherpc',
        'image',
        'link',
        'publictationtext',
        'specialmapsheet',
        'surveyor'
    ];

    /**
     * @param string $id
     * @param string $relationship
     * @param Collection $res
     * @return array
     * @throws InvalidArgumentException
     */
    public function updateRelationship(string $id, string $relationship, Collection $res): array
    {
        $this->testRelationship($relationship);
        $str = self::PREFIX . $id . '.' . $relationship;
        $this->cache->clear($str);
        return $this->cache->get($str, function (ItemInterface $item) use ($id, $res, $str) {
            $item->tag([self::PREFIX . $id, $str]);
            return json_encode($res->toArray());
        });
    }

    /**
     * @param string $id
     * @param string $relationship
     * @return JsonResponse|false
     * @throws InvalidArgumentException
     */
    public function getRelationshipById(string $id, string $relationship): JsonResponse|false
    {
        $this->testRelationship($relationship);
        $item = $this->cache->getItem(self::PREFIX . $id . '.' . $relationship);
        return ($item->isHit()) ? json_decode($item->get()) : false;
    }

    public function getAllRelationships($id)
    {
        $ret = [];
        $count = 0;
        foreach (self::MAP_SUFFIX as $key) {
            $item = $this->cache->getItem(self::PREFIX . $id . '.' . $key);
            $ret[$key] = ($item->isHit()) ? json_decode($item->get()) : false;
            if ($ret[$key]) $count++;
        }
        return ($count) ? $ret : null;
    }

    private function testRelationship($relationship): void
    {
        if (!in_array(self::MAP_SUFFIX, $relationship)) {
            throw new \InvalidArgumentException('Invalid relationship "%s". Available relationships: %s',
                $relationship, implode(', ', self::MAP_SUFFIX));
        }
    }
}