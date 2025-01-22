<?php

namespace App\Services\Cache\FilesCache;

use App\Services\Cache\FilesCache;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\InvalidArgumentException;
use Symfony\Contracts\Cache\ItemInterface;

class DbStatusCache extends FilesCache
{
    const PREFIX= 'caveDB.';
    const TABLES_STATUS= 'tables_status';

    public function updateDataBaseStatus(array $data):array
    {
        $this->cache->clear(self::PREFIX.self::TABLES_STATUS);
        return $this->cache->get(self::PREFIX.self::TABLES_STATUS, function(ItemInterface $item) use ($data) {
            $item->tag(self::PREFIX);
            return $data;
        });
    }

    /**
     * @return false|array
     * <pre>
     * [
     * "countryCount" => int
     * "systemParameterCount" => int
     * "systemParameterCountActive" => int
     * "systemParameterCurrentActiveId" => string|null
     * "fdCount" => int
     * "fdlCount" => int
     * "fdlCountDefinitions" => int
     * "fdlCountLocales" => int
     * "fvcCount" => int
     * "fvcCountField" => int
     * "fvclCount" => int
     * "fvclCountLocales" => int
     * "orgCount" => int
     * "geonames" => []
     * ]
     * </pre>
     * @throws InvalidArgumentException
     */
    public function getDataBaseStatus(): array|false
    {
        $item = $this->cache->getItem(self::PREFIX.self::TABLES_STATUS);
        return ($item->isHit())? $item->get() : false;
    }
}