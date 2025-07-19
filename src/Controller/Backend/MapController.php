<?php
namespace App\Controller\Backend;
use App\Controller\BackendController;
use App\Entity\Map\Map;
use App\Form\backend\Map\MapSearchType;
use App\Form\backend\Map\MapType;
use App\Manager\MapManager;
use App\Services\Cache\FilesCache\Map\MapSerializedCache;
use App\Utils\Helper\MapControllerHelper;
use App\Utils\Json\JsonApi\JsonApiManager;
use App\Utils\Json\JsonApi\JsonApiManagerFactory;
use App\Utils\Json\JsonApi\JsonApiSpec;
use App\Utils\Json\JsonApi\SpecTypes\FieldvaluecodeSpecType;
use App\Utils\Json\JsonApi\SpecTypes\JsonApiTypeRegistry;
use App\Utils\Json\JsonApi\SpecTypes\PersonSpecType;
use App\Utils\Json\JsonErrorSerializer\JsonErrorBag;
use App\Utils\Json\Serializers\Map\MapSerializer;
use App\vendor\tobscure\jsonapi\Collection;
use App\vendor\tobscure\jsonapi\Document;
use App\vendor\tobscure\jsonapi\Resource;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/admin/map')]
class MapController extends BackendController
{
    #[Route(path: '/', name: 'admin_map_index')]
    public function indexAction(Request $request): Response
    {
        $form = $this->createForm(MapSearchType::class, new Map());
        return $this->render('@admin/map/index.html.twig',['form'   => $form->createView()]);
    }

    /**
     * @throws \Exception
     */
    #[Route(path: '/list', name: 'admin_map_list_json')]
    public function listJsonAction(Request $request, MapManager $manager, UrlGeneratorInterface $urlGenerator): JsonResponse
    {
        $this->acceptOnlyXmlHttpRequest($request);
        $listOptions= $this->getRequestListOptions($request);
        $form= $this->createForm(MapSearchType::class, new Map())
            ->handleRequest($request);

        list($paginator, $data) = $manager->paginate($form->getData(), $listOptions);

        $collection = (new Collection($data, new MapSerializer($urlGenerator)))
            ->fields(['citation'=>['id','title','subtitle','jsondata']]);
        $document = (new Document($collection));
        $document->addMeta('pagination', $paginator->toArray());

        return new JsonResponse($document , 200, ['Content-Type'=>$document::MEDIA_TYPE]);
    }

    #[Route(path: '/new', name: 'admin_map_new')]

    public function newAction(Request $request, EntityManagerInterface $em): Response
    {
        $form= $this->createForm(MapType::class, new Map())
            ->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            try {
                $entity = $form->getData();
                $em->persist($entity);
                $em->flush();
                $em->clear();
                return $this->redirectToRoute('admin_map_edit', array('id' => $entity->getId()));
            }catch (\Exception $ex){
                $form->addError(new FormError($ex->getMessage()));
            }
        }
        return $this->render('@admin/map/new.html.twig', ['form'=>$form->createView()]);
    }

    #[Route(path: '/view/{id}', name: 'admin_map_view')]

    public function viewAction(string $id, MapManager $manager, UrlGeneratorInterface $urlGenerator): Response
    {
        $cache= $this->getCache();
        $serialized= $cache->getSerializedMap($id);
        if(empty($serialized)){
            $map= $manager->repo->find($id);
            if(!$map){
                throw new NotFoundHttpException((sprintf('Map id "%s" not found', $id)));
            }
            $serialized=  $cache->updateSerializedMap($map,$this->serializeMap($map, $urlGenerator));
        }
        //Registra extensiones de JsonApiSpec en JsonApiManagerFactory para que __toString funcione correctamente
        JsonApiTypeRegistry::registerAll();
        $jam= new JsonApiManager($serialized['data'], $serialized['included']??null);
        /**
         * Retorna un mapa como objeto JsonApiSpec
         * @var JsonApiSpec $jas
         */
        $jas= $jam->getParsed();


        return $this->render('@admin/map/view.html.twig', [
            'id'=>$id,
            'cache'=>$serialized,
            'jas'=> $jas
        ]);
    }

    #[Route(path: '/edit/{id}', name: 'admin_map_edit')]
    public function editAction(Request $request, Map $entity, EntityManagerInterface $em, ParameterBagInterface $bag, UrlGeneratorInterface $urlGenerator): Response
    {
        $form= $this->createForm(MapType::class, $entity)->handleRequest($request);

        if (!$request->isXmlHttpRequest()){
            return $this->render('@admin/map/edit.html.twig', ['form' => $form->createView(), 'entity' => $entity]);
        }
        if (!$form->isSubmitted() || !$form->isValid())
        {
            return $this->getJsonFormErrorResponse($form);
        }
        try{
            $entity= $form->getData();

            $em->persist($entity);
            $em->flush();
            $em->clear();

            /**
             * para agregar nuevos elementos a la cache:
             * MapControllerHelper::MAP_SERIALIZER_FIELDS['with']... propiedades
             * MapControllerHelper::MAP_SERIALIZER_FIELDS['fields']...campos de propiedad si es relationship con otra tabla.
             * En caso de relaciones, agregar en MapSerializer su correspondiente clase de serialización.
            */
            $cache= $this->getCache();
            $document= $this->serializeMap($entity, $urlGenerator);
            $cache->updateSerializedMap($entity, $document);
            return new JsonResponse(null , 200);

        }catch (\Exception $e){
            return $this->getJsonExceptionErrorResponse($e);
        }
    }

    #[Route(path: '/delete/{id}', name: 'admin_map_delete')]
    public function deleteAction(Request $request, Map $entity, EntityManagerInterface $em, TranslatorInterface $translator): RedirectResponse
    {
        return call_user_func_array([$this, 'CommonBackendDeleteAction'], array_merge(func_get_args(), [
            'routeSuccess'=>'admin_map_index',
            'routeError'=>'admin_map_edit',
        ]));
    }

    private function  serializeMap(Map $map, UrlGeneratorInterface $urlGenerator): Document
    {
        $serializer= new MapSerializer($urlGenerator);
        $resource = new Resource($map, $serializer);
        $resource->with(
            MapControllerHelper::MAP_SERIALIZER_FIELDS['with']
        )->fields(
            MapControllerHelper::MAP_SERIALIZER_FIELDS['fields']
        )
        ;
        return new Document($resource);
    }

    public function getCache(): MapSerializedCache
    {
        $project_dir= $this->getParameter('kernel.project_dir');
        $project_env= $this->getParameter('kernel.environment');
        return new MapSerializedCache($project_dir, $project_env);
    }
}