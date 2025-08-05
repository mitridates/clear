<?php

namespace App\Geonames\Infrastructure\Controller;
use App\Geonames\Domain\Manager\{Admin3Manager};
use App\Geonames\Domain\Manager\Admin1Manager;
use App\Geonames\Domain\Manager\Admin2Manager;
use App\Geonames\Infrastructure\Serializer\GeonamesSerializable;
use App\Services\Cache\FilesCache\GeonamesJsonCache;
use App\Shared\JsonApi\ErrorSerializer\{JsonErrorMessages};
use App\Shared\JsonApi\ErrorSerializer\JsonErrorBag;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[Route('/json/geonames')]
class JsonGeonamesController extends AbstractController
{
    /**
     * Search Admin1 by country id
     */
    #[Route(path: '/Admin1ChildrenJSON', name: 'json_geonames_admin1_children', methods: ["GET","POST"])]
    public function admin1ChildrenJsonAction(Request $request, Admin1Manager $manager, UrlGeneratorInterface $urlGenerator, GeonamesJsonCache $cache): JsonResponse
    {
        $country= $request->get('countryid');
        $pretty= $request->get('prettyPrint', false);

        if(!$country) {
            return (new JsonErrorBag())->addJsonError(
                JsonErrorMessages::invalidParameter(
                    $urlGenerator->generate('json_geonames_admin1_children', null, $urlGenerator::RELATIVE_PATH), 'countryid')
            )->getJsonResponse();
        }
        if(!$data= $cache->getAdmin1ByCountry($country)){
            $res= GeonamesSerializable::serializeAdmin1($manager->repo->findBy(['country'=>$country], ['name'=>'ASC']), $pretty, [
                '_cached', (new \DateTimeImmutable())->format(\DateTimeInterface::RFC850)
            ]);
            $data= $cache->updateAdmin1ByCountry($country, $res);
        }
        return $data;
    }

    /**
     * Search Admin2 by admin1 id
     */
    #[Route(path: '/Admin2ChildrenJSON', name: 'json_geonames_admin2_children', methods: ["GET","POST"])]
    public function jsonAdmin2Children(Request $request, Admin2Manager $manager, UrlGeneratorInterface $urlGenerator, GeonamesJsonCache $cache): JsonResponse
    {
        $admin1= $request->get('admin1id');
        $pretty= $request->get('prettyPrint');

        if(!$admin1) {
            return (new JsonErrorBag())->addJsonError(
                JsonErrorMessages::invalidParameter(
                    $urlGenerator->generate('json_geonames_admin2_children', null, $urlGenerator::RELATIVE_PATH), 'admin1id')
            )->getJsonResponse();
        }
        if(!$data= $cache->getAdmin2ByAdmin1($admin1)){
            $res= GeonamesSerializable::serializeAdmin2($manager->repo->findBy(['admin1'=>$admin1], ['name'=>'ASC']), $pretty, [
                '_cached', (new \DateTimeImmutable())->format(\DateTimeInterface::RFC850)
            ]);
            $data= $cache->updateAdmin2ByAdmin1($admin1, $res);
        }
        return $data;
    }


    /**
     * Search Admin3 by Admin2 id
     */
    #[Route(path: '/Admin3ChildrenJSON', name: 'json_geonames_admin3_children', methods: ["GET","POST"])]
    public function jsonAdmin3Children(Request $request, Admin3Manager $manager, UrlGeneratorInterface $urlGenerator,  GeonamesJsonCache $cache): JsonResponse
    {
        $adm2= $request->get('admin2id');
        $pretty= $request->get('prettyPrint');

        if(!$adm2) {
            return (new JsonErrorBag())->addJsonError(
                JsonErrorMessages::invalidParameter(
                    $urlGenerator->generate('json_geonames_admin3_children', null, $urlGenerator::RELATIVE_PATH), 'admin2id')
            )->getJsonResponse();
        }

        if(!$data= $cache->getAdmin3ByAdmin1($adm2)){
            $res= GeonamesSerializable::serializeAdmin3($manager->repo->findBy(['admin2'=>$adm2], ['name'=>'ASC']), $pretty, [
                '_cached', (new \DateTimeImmutable())->format(\DateTimeInterface::RFC850)
            ]);
            $data= $cache->updateAdmin3ByAdmin2($adm2, $res);
        }
        return $data;//GeonamesSerializable::serializeAdmin3($manager->repo->findBy(['admin2'=>$str]), $pretty);
    }
}
