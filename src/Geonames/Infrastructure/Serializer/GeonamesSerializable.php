<?php
namespace App\Geonames\Infrastructure\Serializer;

use App\Shared\tobscure\jsonapi\AbstractSerializer;
use App\Shared\tobscure\jsonapi\Collection;
use App\Shared\tobscure\jsonapi\Document;
use Symfony\Component\HttpFoundation\JsonResponse;

class GeonamesSerializable
{
    public static function serializeAdmin1(array $data, ?bool $pretty, ?array $meta): JsonResponse
    {
        $w = ['admin1'];
        $f = ['admin1' => ['name']];
        $coll= self::setCollection($data, new Admin1Serializer(), $w, $f);
        $doc= new Document($coll);
        if($meta){
            foreach ($meta as $k=>$v){
                $doc->addMeta($k, $v);
            }
        }
        return self::getJsonResponse( $doc, $pretty??null);
    }

    public static function serializeAdmin2(array $data, ?bool $pretty, ?array $meta): JsonResponse
    {
        $w = ['admin2'];
        $f = ['admin2' => ['name']];
        $coll= self::setCollection($data, new Admin2Serializer(), $w, $f);
        $doc= new Document($coll);
        if($meta){
            foreach ($meta as $k=>$v){
                $doc->addMeta($k, $v);
            }
        }
        return self::getJsonResponse($doc, $pretty??null);
    }

    public static function serializeAdmin3(array $data, ?bool $pretty, ?array $meta): JsonResponse
    {
        $w = ['admin3'];
        $f = ['admin3' => ['name']];
        $coll= self::setCollection($data, new Admin3Serializer(), $w, $f);
        $doc= new Document($coll);
        if($meta){
            foreach ($meta as $k=>$v){
                $doc->addMeta($k, $v);
            }
        }
        return self::getJsonResponse($doc, $pretty??null);
    }

    private static function setCollection(array $data, AbstractSerializer $serializer, ?array $with=[], ?array $fields=[]): Collection
    {
        $coll= new Collection($data, $serializer);
        if ($with) $coll->with($with);
        if ($fields) $coll->fields($fields);
        return $coll;
    }

    protected static function getJsonResponse(Document $doc , ?bool $pretty): JsonResponse
    {
        $arr= $doc->toArray();
        $res= new JsonResponse(array_merge($arr, ['dataLength'=>count($arr['data'])]), 200);
        if($pretty)$res->setEncodingOptions( $res::DEFAULT_ENCODING_OPTIONS | JSON_PRETTY_PRINT );
        return $res;
    }
}