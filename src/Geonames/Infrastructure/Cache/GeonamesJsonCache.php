<?php

namespace App\Geonames\Infrastructure\Cache;

use App\Shared\Infrastructure\Cache\FilesCache;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Cache\ItemInterface;

class GeonamesJsonCache extends FilesCache
{
    const PREFIX= 'geonames.';
    const SUFFIX_ADM1= 'adm1.';
    const SUFFIX_ADM2= 'adm2.';
    const SUFFIX_ADM3= 'adm3.';
    public function updateAdmin1ByCountry(string $country, JsonResponse $res):JsonResponse
    {
        $this->cache->clear(self::PREFIX.self::SUFFIX_ADM1.$country);
        return $this->cache->get(self::PREFIX.self::SUFFIX_ADM1.$country, function(ItemInterface $item) use ($country, $res) {
            $item->tag([self::PREFIX, 'admin1']);
            return $res;
        });
    }

    /**
     * @param string $country
     * @return false|JsonResponse
     * @throws InvalidArgumentException
     */
    public function getAdmin1ByCountry(string $country): JsonResponse|false
    {
        $item = $this->cache->getItem(self::PREFIX.self::SUFFIX_ADM1.$country);
        return ($item->isHit())? $item->get() : false;
    }

    public function updateAdmin2ByAdmin1(string $admin1, JsonResponse $res):JsonResponse
    {
        $this->cache->clear(self::PREFIX.self::SUFFIX_ADM2.$admin1);
        return $this->cache->get(self::PREFIX.self::SUFFIX_ADM2.$admin1, function(ItemInterface $item) use ($admin1, $res) {
            $item->tag([self::PREFIX, 'admin2']);
            return $res;
        });
    }

    /**
     * @param string $country
     * @return false|JsonResponse
     * @throws InvalidArgumentException
     */
    public function getAdmin2ByAdmin1(string $admin1): JsonResponse|false
    {
        $item = $this->cache->getItem(self::PREFIX.self::SUFFIX_ADM2.$admin1);
        return ($item->isHit())? $item->get() : false;
    }

    public function updateAdmin3ByAdmin2(string $admin2, JsonResponse $res):JsonResponse
    {
        $this->cache->clear(self::PREFIX.self::SUFFIX_ADM3.$admin2);
        return $this->cache->get(self::PREFIX.self::SUFFIX_ADM3.$admin2, function(ItemInterface $item) use ($admin2, $res) {
            $item->tag([self::PREFIX, 'admin3']);
            return $res;
        });
    }

    /**
     * @param string $admin2
     * @return false|JsonResponse
     * @throws InvalidArgumentException
     */
    public function getAdmin3ByAdmin1(string $admin2): JsonResponse|false
    {
        $item = $this->cache->getItem(self::PREFIX.self::SUFFIX_ADM3.$admin2);
        return ($item->isHit())? $item->get() : false;
    }
}