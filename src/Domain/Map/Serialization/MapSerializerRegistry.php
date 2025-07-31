<?php

namespace App\Domain\Map\Serialization;

use App\Domain\JsonApi\Serializers\Map\MapCaveSerializer;
use App\Domain\JsonApi\Serializers\Map\MapCitationSerializer;
use App\Domain\JsonApi\Serializers\Map\MapCommentSerializer;
use App\Domain\JsonApi\Serializers\Map\MapControllerSerializer;
use App\Domain\JsonApi\Serializers\Map\MapDrafterSerializer;
use App\Domain\JsonApi\Serializers\Map\MapFurthergcSerializer;
use App\Domain\JsonApi\Serializers\Map\MapFurtherpcSerializer;
use App\Domain\JsonApi\Serializers\Map\MapImageSerializer;
use App\Domain\JsonApi\Serializers\Map\MapLinkSerializer;
use App\Domain\JsonApi\Serializers\Map\MapPublicationtextSerializer;
use App\Domain\JsonApi\Serializers\Map\MapSpecialmapsheetSerializer;
use App\Domain\JsonApi\Serializers\Map\MapSurveyorSerializer;

class MapSerializerRegistry
{
    //One-To-One (OTO) serializers
   public const OTO_SERIALIZER=[
        'controller'=>MapControllerSerializer::class,
        'specialmapsheet'=>MapSpecialmapsheetSerializer::class,
        'comment'=>MapCommentSerializer::class,
        'publicationtext'=>MapPublicationtextSerializer::class,
    ];

    // Serializer Fields & relationship
    public const OTO_SERIALIZER_FIELDS=[
        'controller'=>['with'=>[],'fields'=>[]],
        'specialmapsheet'=>['with'=>[],'fields'=>[]],
        'comment'=>['with'=>[],'fields'=>[]],
        'publicationtext'=>['with'=>[],'fields'=>[]]
    ];
// Many-To-One (MTO) serializers
public const MTO_SERIALIZER=[
        'cave'=> MapCaveSerializer::class,
        'citation'=>MapCitationSerializer::class,
        'comment'=>MapCommentSerializer::class,
        'drafter'=>MapDrafterSerializer::class,
        'furthergc'=>MapFurthergcSerializer::class,
        'furtherpc'=>MapFurtherpcSerializer::class,
        'image'=>MapImageSerializer::class,
        'link'=>MapLinkSerializer::class,
        'surveyor'=>MapSurveyorSerializer::class
    ];

// Serializer Fields & relationship
    public const MAP_SERIALIZER_FIELDS=[
        'with'=>['country', 'admin1', 'admin2', 'admin3', 'sourcecountry', 'sourceorg', 'type',
            'sourcetype', 'principalsurveyorid', 'principaldrafterid'],
        'fields'=>[
            'country'=>['id','name'],
            'sourcecountry'=>['id','name'],
            'sourceorg'=>['id','name'],
            'admin1'=>['id','name'],
            'admin2'=>['id','name'],
            'admin3'=>['id','name'],
            'type'=>['code', 'value'],
            'sourcetype'=>['code', 'value'],

        ]
    ];
// Serializer Fields & relationship
    public  const MTO_SERIALIZER_FIELDS=[
        'image'=>[
            'with'=>['digitaltechnique', 'type', 'format'],
            'fields'=>[
                'fieldvaluecode'=>['code', 'value'],
            ]
        ],
        'surveyor'=>[
            'with'=>['surveyorid'],
            'fields'=>[
                'person'=>['name', 'surname'],
                'mapsurveyor'=>[ 'map', 'surveyor', 'surveyorid', 'sequence']
            ]
        ],
        'drafter'=>[
            'with'=>['drafterid'],
            'fields'=>[
                'person'=>['name', 'surname'],
                'mapdrafter'=>[ 'map', 'drafter', 'drafterid', 'sequence']
            ]
        ],
        'link'=>[
            'with'=>['comment', 'link'],
            'fields'=>[
                'link'=>['title', 'organisation','organisationname', "author", "authorname", 'url'],
            ]
        ],
        'citation'=>[
            'with'=>['citation'],
        ],
        'furtherpc'=>[
            'with'=>['country', 'admin1', 'area'],
            'fields'=>[
                'mapfurtherpc'=>['map', 'country', 'admin1', 'area'],
                'country'=>['name'],
                'admin1'=>['name'],
                'area'=>['name'],
            ]
        ],
        'furthergc'=>[
            'fields'=>[
                'mapfurthergc'=>['map', 'northlatitude', 'southlatitude', 'eastlongitude', 'westlongitude']
            ]
        ],
        'cave'=>[
            'with'=>['cave'],
            'fields'=>[
                'cave'=>['name'],
            ]
        ]
    ];

    /**
     * @param string $key
     * @param string|null $type
     * @return string|array
     * @throws \InvalidArgumentException
     */
    public static function getSerializer(string $key, ?string $type=null): string|array
    {
        return match ($type) {
            'oto' => self::OTO_SERIALIZER[$key]
                ?? throw new \InvalidArgumentException("No serializer type found for key '{$key}' with type '{$type}'."),
            'mto' => self::MTO_SERIALIZER[$key]
                ?? throw new \InvalidArgumentException("No serializer type found for key '{$key}' with type '{$type}'."),
            default => throw new \InvalidArgumentException("No serializer found for key '{$key}' with type '{$type}'."),
        };
    }
}