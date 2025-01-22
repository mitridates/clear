<?php

namespace App\Controller\Json;
use App\Entity\FieldDefinition\Fielddefinition;
use App\Manager\FieldDefinitionManager;
use App\Services\Cache\FilesCache\FieldDefinitionCache;
use App\Utils\Json\JsonErrorSerializer\JsonErrorBag;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[Route('/admin/json/serialize')]
class JsonFieldDefinitionSerializerController extends AbstractController
{
    /**
     * Serialize Fielddefinition
     * @throws \Exception
     */
    #[Route(path: '/getFieldDefinitionByCode', name: 'serialize_json_fielddefinition_by_code_', methods: ["GET","POST"])]

    public function getFieldDefinitionByCodeToJsonAction(Request $request,
         EntityManagerInterface $em, UrlGeneratorInterface $urlGenerator, ParameterBagInterface $bag) : JsonResponse
    {

        $env= $bag->get('kernel.environment');
        $dir= $bag->get('kernel.project_dir');
        $fieldDefinitionCache  = new FieldDefinitionCache($dir, $env);
        $fieldDefinitionManager= new FieldDefinitionManager($em);
        $locale = strtolower($request->get('locale'));
        $code= $request->get('code');
        $fdCache= $fieldDefinitionCache->getFieldDefinition($code, $locale);
        $warmup = strtolower($request->get('warmup'));

//        $warmup='warnup--all';
//        $warmup=1;


        if($warmup=== 'warnup--all'){
            $fieldDefinitionCache->warmupAll($fieldDefinitionManager, $urlGenerator);
        }
        //get Field definition from cache or add new one
        if(!$fdCache || $warmup)
        {

            /** @var Fielddefinition|null $fd */
            $fd = $fieldDefinitionManager->repo->find($code);

            if(!$fd){
                return (new JsonErrorBag($env))->addMsg(sprintf('Code "%s" not found', $code))->getJsonResponse();
            }
            $fdCache= $fieldDefinitionCache->warmup($fieldDefinitionManager, $fd, $urlGenerator)->getFieldDefinition($code, $locale);
        }

        return  new JsonResponse($fdCache, 200, ['Content-Type'=>'application/vnd.api+json']);
    }


}