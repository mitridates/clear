<?php

namespace App\Controller\Json;
use App\Domain\JsonApi\Serializers\Map\MapImageSerializer;
use App\Domain\Map\Entity\Map\Map;
use App\Entity\Cavern\Trait;
use App\Manager\MapManager;
use App\Shared\tobscure\jsonapi\{Collection, Document};
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[Route('/json/MapFilesPaginator')]
class JsonMapFilesPaginationController extends AbstractController
{

    /**
     * Paginación de imágenes/otros de mapas para una cavidad
     *
     */
    #[Route(path: '/MapFilesInCave/{cave}', name: 'json_pagination_map_files_in_cave', methods: ["GET","POST"])]
    public function mapsFilesInCaveJsonAction(Request $request, Cave $cave, MapManager $manager, UrlGeneratorInterface $urlGenerator) : JsonResponse
    {
        $limit = $request->get('limit', 20);
        $page = $request->get('page', 1);
        $sort = $request->get('sort');
        $orderBy = $request->get('orderby');

        list($paginator, $data) = $manager->pageMapimageByCave($cave,$page, $limit, $orderBy, $sort);

        //JSON-API
        $collection = (new Collection($data,new MapImageSerializer($urlGenerator, null, $request->getLocale())))
            //dot.field (ex.: map.principalsurveyorid)  añade la propiedad a includes.
            // En fields hay que añadir type:[field[]] para serializar los campos que necesitamos
            ->with(['digitaltechnique', 'type', 'format', 'map', 'map.principalsurveyorid', 'map.principaldrafterid'])
            ->fields([
                'fieldvaluecode'=>['code', 'value'],
                'map'=>['name', 'principalsurveyorid', 'principaldrafterid','edition'],//Object se muestra como id, sin nested
                //principalsurveyorid & principaldrafterid son type='person' y apareceran en included con estos attributes
                'person'=>['name', 'surname'],
            ])
        ;
        $document = new Document($collection);
        $document->addMeta('pagination', $paginator->toArray());

        return new JsonResponse($document , 200);

    }

    /**
     * Map files in map
     */
    #[Route(path: '/MapFilesInMap/{map}', name: 'json_pagination_map_files_in_map', methods: ["GET","POST"])]
    public function mapfilesjsonAction(Request $request, Map $map, MapManager $manager) : JsonResponse
    {
        $data= $map->getMapimage();
        //JSON-API
        $collection = (new Collection($data, new MapImageSerializer()));
//        $collection->fields(['mapimage'=>['map','title', 'thumbfilename','filename', 'filename','directorypath','mimetype','filesize ','comment']]);
        $document = new Document($collection);
        $document->addMeta('total', count($data));
        return new JsonResponse($document , 200);
    }

}