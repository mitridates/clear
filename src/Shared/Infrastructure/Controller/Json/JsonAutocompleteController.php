<?php

namespace App\Shared\Infrastructure\Controller\Json;
use App\Area\Domain\Entity\Area;
use App\Area\Domain\Manager\AreaManager;
use App\Area\Infrastructure\Serializer\{AreaSerializer};
use App\Article\Domain\Entity\Article;
use App\Article\Domain\Manager\ArticleManager;
use App\Article\Infrastructure\Serializer\ArticleSerializer;
use App\Cave\Domain\Entity\Cave;
use App\Cave\Domain\Manager\{CaveManager};
use App\Cave\Infrastructure\Serializer\CaveSerializer;
use App\Citation\Domain\Entity\Citation;
use App\Citation\Domain\Manager\CitationManager;
use App\Citation\Infrastructure\Serializer\CitationSerializer;
use App\Fielddefinition\Domain\Entity\{Fielddefinition};
use App\Fielddefinition\Domain\Manager\FieldDefinitionManager;
use App\Fielddefinition\Infrastructure\Serializer\FieldDefinitionSerializer;
use App\Link\Domain\Entity\Link;
use App\Link\Domain\Manager\LinkManager;
use App\Link\Infrastructure\Serializer\LinkSerializer;
use App\Map\Domain\Entity\Map\Map;
use App\Map\Domain\Manager\MapManager;
use App\Map\Infrastructure\Serializer\MapSerializer;
use App\Mapserie\Domain\Entity\Mapserie;
use App\Mapserie\Domain\Manager\MapSerieManager;
use App\Mapserie\Infrastructure\Serializer\MapserieSerializer;
use App\Organisation\Domain\Entity\Organisation;
use App\Organisation\Domain\Manager\OrganisationManager;
use App\Organisation\Infrastructure\Serializer\OrganisationSerializer;
use App\Person\Domain\Entity\Person;
use App\Person\Domain\Manager\PersonManager;
use App\Person\Infrastructure\Serializer\PersonSerializer;
use App\Shared\JsonApi\ErrorSerializer\JsonErrorBag;
use App\Shared\JsonApi\Serializers\MimeTypeSerializer;
use App\Shared\tobscure\jsonapi\Collection;
use App\Shared\tobscure\jsonapi\Document;
use App\Specie\Domain\Entity\Specie;
use App\Specie\Domain\Manager\SpecieManager;
use App\Specie\Infrastructure\Serializer\SpecieSerializer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mime\MimeTypes;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[Route('/admin/json/autocomplete')]
class JsonAutocompleteController extends AbstractController
{

    #[Route(path: '/area', name: 'json_autocomplete_area', methods: ["GET","POST"])]
    public function areaJsonAutocompleteAction(Request $request, AreaManager $manager, UrlGeneratorInterface $urlGenerator) : JsonResponse
    {
        $entity = new Area();
        $code= $request->get('code');
        if(!$code){
            return (new JsonErrorBag())->addException(new BadRequestException('The request must contain "code" parameter'))->getJsonResponse();
        }
        $entity->setName($code);

        $data= $manager->findByArea($entity);
        //JSON-API
        $collection = (new Collection($data, new AreaSerializer($urlGenerator)));
        $collection->fields(['area'=>['id', 'name', 'country', 'admin1']]);
        $document = new Document($collection);
        $document->addMeta('total', count($data));

        return new JsonResponse($document , 200, ['Content-Type'=>$document::MEDIA_TYPE]);
    }
    /**
     * Search Admin2 by admin1 id
     */
    #[Route(path: '/AreaChildrenJSON', name: 'json_geonames_area_children', methods: ["GET","POST"])]
    public function jsonAreaChildren(Request $request, AreaManager $manager, UrlGeneratorInterface $urlGenerator): JsonResponse
    {
        $code= $request->get('code');
        $pretty= $request->get('prettyPrint');
        $data=[];
        if(!$code) {
            return (new JsonErrorBag())->addException(new BadRequestException('The request must contain "code" parameter'))->getJsonResponse();
        }
        if (preg_match('/^\w{2}$/', $code)) {
            $data= $manager->repo->findBy(['country'=>$code]);
        }elseif(preg_match('/^\w{2}\.\w{2,5}$/', $code)){
            $data= $manager->repo->findBy(['admin1'=>$code]);
        }
        $collection = (new Collection($data, new AreaSerializer($urlGenerator)));
        $collection->fields(['area'=>['id', 'name', 'country', 'admin1']]);
        $document = new Document($collection);
        $document->addMeta('total', count($data));

        return new JsonResponse($document , 200, ['Content-Type'=>$document::MEDIA_TYPE]);
    }


    #[Route(path: '/article', name: 'json_autocomplete_article', methods: ["GET","POST"])]
    public function articleJsonAutocompleteAction(Request $request, ArticleManager $manager) : JsonResponse
    {
        $entity = new Article();
        if(!$request->get('term')){
            return (new JsonErrorBag())->addException(new BadRequestException('The request must contain "term" parameter'))->getJsonResponse();
        }
        $entity->setPublicationname($request->get('term'));
        $entity->setArticlename($request->get('term'));
        $data= $manager->findByArticle($entity);
        //JSON-API
        $collection = (new Collection($data, new ArticleSerializer()));
        $collection->fields([
            'country'=>['name'],
            'admin1'=>['name']
        ])
            ->with(['country', 'admin1']);
        ;
        $document = new Document($collection);
        $document->addMeta('total', count($data));

        return new JsonResponse($document , 200, ['Content-Type'=>$document::MEDIA_TYPE]);
    }

    #[Route(path: '/cave', name: 'json_autocomplete_cave', methods: ["GET","POST"])]
    public function caveJsonAutocompleteAction(Request $request, CaveManager $manager, UrlGeneratorInterface $urlGenerator) : JsonResponse
    {
        $entity = new Cave();
        if(!$request->get('term')){
            return (new JsonErrorBag())->addException(new BadRequestException('The request must contain "term" parameter'))->getJsonResponse();
        }
        $entity->setName($request->get('term'));
        $data= $manager->findByCave($entity);
        //JSON-API
        $collection = new Collection($data, new CaveSerializer($urlGenerator, $request->getLocale()));
        $collection->fields(
            [
                'cave'=>['name', 'country', 'admin1', 'admin2', 'admin3'],
                'country'=>['name'],
                'admin1'=>['name'],
                'admin2'=>['name'],
                'admin3'=>['name'],
            ])
            ->with(['country', 'admin1', 'admin2', 'admin3']);
        $document = new Document($collection);
        $document->addMeta('total', count($data));

        return new JsonResponse($document , 200, ['Content-Type'=>$document::MEDIA_TYPE]);
    }

    #[Route(path: '/fielddefinition', name: 'json_autocomplete_fielddefinition', methods: ["GET","POST"])]
    public function fielddefinitionJsonAutocompleteAction(Request $request, FielddefinitionManager $manager) : JsonResponse
    {
        $entity = new Fielddefinition();
        if(!$request->get('term')){
            return (new JsonErrorBag($this->getParameter('kernel.environment')))->
            addException(new BadRequestException('The request must contain "term" parameter'))
                ->getJsonResponse();
        }
        $entity->setName($request->get('term'));
        $data= $manager->findByFielddefinition($entity);
        //JSON-API
        $collection = (new Collection($data, new FielddefinitionSerializer()));
        $collection->fields(['fielddefinition'=>['code', 'name']]);
        $document = new Document($collection);
        $document->addMeta('total', count($data));

        return new JsonResponse($document , 200, ['Content-Type'=>$document::MEDIA_TYPE]);
    }


    #[Route(path: '/organisation', name: 'json_autocomplete_organisation', methods: ["GET","POST"])]
    public function organisationJsonAutocompleteAction(Request $request, OrganisationManager $manager) : JsonResponse
    {
        $organisation = new Organisation();

        if(!$request->get('term')){
            return (new JsonErrorBag())->addException(new BadRequestException('The request must contain "term" parameter'))->getJsonResponse();
        }

        $organisation->setName($request->get('term'));
        $data= $manager->findByOrganisation($organisation);
        //JSON-API
        $collection = (new Collection($data, new OrganisationSerializer()));
        $collection->fields(
            [
                'organisation'=>['name', 'country', 'admin1', 'admin2', 'admin3'],
                'country'=>['name'],
                'admin1'=>['name'],
                'admin2'=>['name'],
                'admin3'=>['name'],
            ])
            ->with(['country', 'admin1', 'admin2', 'admin3'])
        ;
        $document = new Document($collection);
        $document->addMeta('total', count($data));

        return new JsonResponse($document , 200, ['Content-Type'=>$document::MEDIA_TYPE]);
    }

    #[Route(path: '/mapserie', name: 'json_autocomplete_mapserie', methods: ["GET","POST"])]
    public function mapSerieJsonAutocompleteAction(Request $request, MapSerieManager $manager) : JsonResponse
    {
        $entity = new Mapserie();
        if(!$request->get('term')){
            return (new JsonErrorBag())->addException(new BadRequestException('The request must contain "term" parameter'))->getJsonResponse();
        }
        $entity->setName($request->get('term'));
        $data= $manager->findByMapserie($entity);
        //JSON-API
        $collection = (new Collection($data, new MapserieSerializer()));
        $collection->fields(['mapserie'=>['id', 'name']]);
        $document = new Document($collection);
        $document->addMeta('total', count($data));

        return new JsonResponse($document , 200, ['Content-Type'=>$document::MEDIA_TYPE]);
    }

    #[Route(path: '/mime', name: 'json_autocomplete_mime', methods: ["GET","POST"])]
    public function mimeJsonAutocompleteAction(Request $request) : JsonResponse
    {
        if(!$request->get('term')){
            return (new JsonErrorBag())->addException(new BadRequestException('The request must contain "term" parameter'))->getJsonResponse();
        }
        $m= new MimeTypes();
        $mime= $request->get('term');
        $data=[];
        $arr= $m->getMimeTypes($mime);
        foreach ($arr as $k=>$v) {
            $data[][$v]=$v;
        }
        //JSON-API
        $collection = (new Collection($data, new MimeTypeSerializer()));
        $collection->fields(['mimetype'=>['id', 'name']]);
        $document = new Document($collection);
        $document->addMeta('total', count($arr));

        return new JsonResponse($document , 200, ['Content-Type'=>$document::MEDIA_TYPE]);
    }

    #[Route(path: '/map', name: 'json_autocomplete_map', methods: ["GET","POST"])]
    public function mapJsonAutocompleteAction(Request $request, MapManager $manager) : JsonResponse
    {
        $entity = new Map();
        if(!$request->get('term')){
            return (new JsonErrorBag())->addException(new BadRequestException('The request must contain "term" parameter'))->getJsonResponse();
        }
        $entity->setName($request->get('term'));
        $data= $manager->findByMap($entity);
        //JSON-API
        $collection = (new Collection($data, new MapSerializer()));
        $collection->fields(['map'=>['id', 'name']]);
        $document = new Document($collection);
        $document->addMeta('total', count($data));

        return new JsonResponse($document , 200, ['Content-Type'=>$document::MEDIA_TYPE]);

    }

    /**
     * Specie
     *
     * @Route("/json/autocomplete/specie",name="dashboard_autocomplete_specie")
     * @param Request $request
     * @param SpecieManager $manager
     * @return JsonResponse
     */
    #[Route(path: '/specie', name: 'json_autocomplete_specie', methods: ["GET","POST"])]
    public function specieJsonAutocompleteAction(Request $request, SpecieManager $manager) : JsonResponse
    {
        $entity = new Specie();
        if(!$request->get('term')){
            return (new JsonErrorBag())->addException(new BadRequestException('The request must contain "term" parameter'))->getJsonResponse();
        }
        $entity->setName($request->get('term'));
        $data= $manager->findBySpecie($entity);
        //JSON-API
        $collection = (new Collection($data, new SpecieSerializer()));
        $collection->fields([
            'specie'=>[
                'id', 'name','commonname' , 'phylum', 'genus', 'family', 'orden', 'class'
            ]
        ]);
        $document = new Document($collection);
        $document->addMeta('total', count($data));

        return new JsonResponse($document , 200, ['Content-Type'=>$document::MEDIA_TYPE]);

    }

    #[Route(path: '/person', name: 'json_autocomplete_person', methods: ["GET","POST"])]
    public function personJsonAutocompleteAction(Request $request, PersonManager $manager) : JsonResponse
    {
        $alias = 'prsn';
        $entity = new Person();
        if(!$request->get('term')){
            return (new JsonErrorBag())->addException(new BadRequestException('The request must contain "term" parameter'))->getJsonResponse();
        }
        $entity->setName($request->get('term'));
        $entity->setSurname($request->get('term'));
        $data= $manager->findByPerson($entity, $alias);
        //JSON-API
        $collection = (new Collection($data, new PersonSerializer()));
        $collection->fields(
            [
                'person'=>[ 'name', 'surname', 'cityorsuburb' ,'country', 'admin1', 'admin2', 'admin3'],
                'country'=>['name'],
                'admin1'=>['name'],
                'admin2'=>['name'],
                'admin3'=>['name'],
            ])
            ->with(['country', 'admin1', 'admin2', 'admin3']);
        $document = new Document($collection);
        $document->addMeta('total', count($data));

        return new JsonResponse($document , 200, ['Content-Type'=>$document::MEDIA_TYPE]);
    }

    #[Route(path: '/link', name: 'json_autocomplete_link', methods: ["GET","POST"])]
    public function linkJsonAutocompleteAction(Request $request, LinkManager $manager) : JsonResponse
    {
        $entity = new Link();
        if(!$request->get('term')){
            return (new JsonErrorBag())->addException(new BadRequestException('The request must contain "term" parameter'))->getJsonResponse();
        }
        $entity->setTitle($request->get('term'));
        $data= $manager->findByLink($entity);
        //JSON-API
        $collection = (new Collection($data, new LinkSerializer()));
        $collection->fields([
            'link'=>['id', 'title', 'author', 'authorname', 'organisation', 'organisationname', 'url', 'mime'],
            'author'=>['name', 'surname'],
            'organisation'=>['name', 'country', 'admin1', 'admin2', 'admin3'],
        ]);
        $document = new Document($collection);
        $document->addMeta('total', count($data));

        return new JsonResponse($document , 200, ['Content-Type'=>$document::MEDIA_TYPE]);
    }


    #[Route(path: '/citation', name: 'json_autocomplete_citation', methods: ["GET","POST"])]
    public function citationJsonAutocompleteAction(Request $request, CitationManager $manager, UrlGeneratorInterface $urlGenerator) : JsonResponse
    {
        $entity = new Citation();
        if(!$request->get('term')){
            return (new JsonErrorBag())->addException(new BadRequestException('The request must contain "term" parameter'))->getJsonResponse();
        }
        $entity->setTitle($request->get('term'));
        $entity->setSubtitle($request->get('term'));
        $data= $manager->findByCitation($entity);
        //JSON-API
        $collection = (new Collection($data, new CitationSerializer($urlGenerator)));
        $collection->fields([
            'country'=>['name'],
        ])
            ->with(['country']);
        ;
        $document = new Document($collection);
        $document->addMeta('total', count($data));

        return new JsonResponse($document , 200, ['Content-Type'=>$document::MEDIA_TYPE]);
    }
}