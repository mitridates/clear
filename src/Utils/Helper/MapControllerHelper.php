<?php
namespace App\Utils\Helper;

use App\Entity\Map\Map;
use App\Entity\Map\Mapcave;
use App\Entity\Map\Mapcitation;
use App\Entity\Map\Mapcomment;
use App\Entity\Map\Mapcontroller;
use App\Entity\Map\Mapdetails;
use App\Entity\Map\Mapdrafter;
use App\Entity\Map\Mapfurthergc;
use App\Entity\Map\Mapfurtherpc;
use App\Entity\Map\Mapimage;
use App\Entity\Map\Maplink;
use App\Entity\Map\Mappublicationtext;
use App\Entity\Map\Mapspecialmapsheet;
use App\Entity\Map\Mapsurveyor;
use App\Form\backend\Map\MapCaveType;
use App\Form\backend\Map\MapCitationType;
use App\Form\backend\Map\MapCommentType;
use App\Form\backend\Map\MapControllerType;
use App\Form\backend\Map\MapPartialCoordinatesType;
use App\Form\backend\Map\MapDrafterTypeToOne;
use App\Form\backend\Map\MapFurthergcType;
use App\Form\backend\Map\MapFurtherpcType;
use App\Form\backend\Map\MapImageType;
use App\Form\backend\Map\MapLinkType;
use App\Form\backend\Map\MapPartialSourceType;
use App\Form\backend\Map\MapPartialSurveyType;
use App\Form\backend\Map\MapPublicationtextType;
use App\Form\backend\Map\MapSpecialmapsheetType;
use App\Form\backend\Map\MapSurveyorType;
use App\Form\backend\Map\MapType;
use App\Utils\Json\Serializers\Map\MapCaveSerializer;
use App\Utils\Json\Serializers\Map\MapCitationSerializer;
use App\Utils\Json\Serializers\Map\MapCommentSerializer;
use App\Utils\Json\Serializers\Map\MapControllerSerializer;
use App\Utils\Json\Serializers\Map\MapDrafterSerializer;
use App\Utils\Json\Serializers\Map\MapFurthergcSerializer;
use App\Utils\Json\Serializers\Map\MapFurtherpcSerializer;
use App\Utils\Json\Serializers\Map\MapImageSerializer;
use App\Utils\Json\Serializers\Map\MapLinkSerializer;
use App\Utils\Json\Serializers\Map\MapPublicationtextSerializer;
use App\Utils\Json\Serializers\Map\MapSerializer;
use App\Utils\Json\Serializers\Map\MapSpecialmapsheetSerializer;
use App\Utils\Json\Serializers\Map\MapSurveyorSerializer;

class MapControllerHelper
{

    const OTO_RELATIONSHIP= [
        'details'=>Mapdetails::class,
        'specialmapsheet'=>Mapspecialmapsheet::class,
        'controller'=>Mapcontroller::class,
        'comment'=>Mapcomment::class,
        'publicationtext'=>Mappublicationtext::class,
    ];
    const OTO_FORM_TYPE=[
        'identity'=> MapType::class,
        'controller'=>MapControllerType::class,
        'specialmapsheet'=>MapSpecialmapsheetType::class,
        'comment'=>MapCommentType::class,
        'publicationtext'=>MapPublicationtextType::class,
    ];

    const PARTIAL_FORM_TYPE= [
        'coordinates'=> MapPartialCoordinatesType::class,
        'survey'=> MapPartialSurveyType::class,
        'source'=> MapPartialSourceType::class,
    ];

    const MTO_RELATIONSHIP= [
        'cave'=> Mapcave::class,
        'citation'=>Mapcitation::class,
        'comment'=>Mapcomment::class,
        'drafter'=>Mapdrafter::class,
        'furthergc'=>Mapfurthergc::class,
        'furtherpc'=>Mapfurtherpc::class,
        'image'=>Mapimage::class,
        'link'=>Maplink::class,
        'surveyor'=>Mapsurveyor::class,
    ];
    const MTO_FORM_TYPE=[
        'cave'=> MapCaveType::class,
        'citation'=>MapCitationType::class,
        'comment'=>MapCommentType::class,
        'drafter'=>MapDrafterTypeToOne::class,
        'furthergc'=>MapFurthergcType::class,
        'furtherpc'=>MapfurtherpcType::class,
        'image'=>MapImageType::class,
        'link'=>MapLinkType::class,
        'surveyor'=>MapSurveyorType::class
    ];
    const MAP_SERIALIZER= MapSerializer::class;

    const OTO_SERIALIZER=[
        'controller'=>MapControllerSerializer::class,
        'specialmapsheet'=>MapSpecialmapsheetSerializer::class,
        'comment'=>MapCommentSerializer::class,
        'publicationtext'=>MapPublicationtextSerializer::class,
    ];

    const MTO_SERIALIZER=[
        'cave'=> MapCaveSerializer::class,
        'citation'=>MapCitationSerializer::class,
        'comment'=>MapCommentType::class,
        'drafter'=>MapDrafterSerializer::class,
        'furthergc'=>MapFurthergcSerializer::class,
        'furtherpc'=>MapFurtherpcSerializer::class,
        'image'=>MapImageSerializer::class,
        'link'=>MapLinkSerializer::class,
        'surveyor'=>MapSurveyorSerializer::class
    ];

    const MAP_SERIALIZER_FIELDS=[
        'with'=>['country', 'admin1', 'admin2', 'admin3'],
        'fields'=>[
            //'map'=>['name', 'number', 'country', 'country', 'admin1', 'admin2', 'admin3'],
            'country'=>['id','name'],
            'admin1'=>['id','name'],
            'admin2'=>['id','name'],
            'admin3'=>['id','name']
        ]
    ];


    const OTO_SERIALIZER_FIELDS=[
        'controller'=>['with'=>[],'fields'=>[]],
        'specialmapsheet'=>['with'=>[],'fields'=>[]],
        'comment'=>['with'=>[],'fields'=>[]],
        'publicationtext'=>['with'=>[],'fields'=>[]]
        ];

    const MTO_SERIALIZER_FIELDS=[
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


}


