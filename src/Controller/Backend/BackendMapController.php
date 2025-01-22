<?php
namespace App\Controller\Backend;
use App\Entity\Map\Map;
use App\Form\backend\Map\MapSearchType;
use App\Form\backend\Map\MapType;
use App\Manager\MapManager;
use App\Services\Cache\FilesCache\Map\MapSerializedCache;
use App\Utils\Json\Serializers\Map\MapSerializer;
use App\vendor\tobscure\jsonapi\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/admin/map')]
class BackendMapController extends AbstractController
{
    use BackendControllerTrait;
    #[Route(path: '/', name: 'admin_map_index')]
    public function indexAction(Request $request): Response
    {
        $form = $this->createForm(MapSearchType::class, new Map());
        return $this->render('@admin/map/index.html.twig',['form'   => $form->createView()]);
    }

    #[Route(path: '/list', name: 'admin_map_list_json')]
    public function listMapsAction(Request $request, MapManager $manager, ParameterBagInterface $bag,  UrlGeneratorInterface $urlGenerator,): Response
    {
        return call_user_func_array([$this, '_listJsonRequest'], [
            'request'=>$request,
            'getCollection'=>function ($data) use ($urlGenerator){
                return (new Collection($data, new MapSerializer($urlGenerator)))
                    ->fields([
                        'map'=>['name', 'country', 'admin1', 'admin2', 'admin3'],
                        'country'=>['id','name'],
                        'admin1'=>['id','name'],
                        'admin2'=>['id','name'],
                        'admin3'=>['id','name']
                    ])
                    ->with(['country', 'admin1', 'admin2', 'admin3'])
                    ;
            },
            'form'=>$this->createForm(MapSearchType::class, new Map())->handleRequest($request),
            'bag'=>$bag,
            'manager'=>$manager
        ]);
    }

    #[Route(path: '/new', name: 'admin_map_new')]

    public function newAction(Request $request, EntityManagerInterface $em): Response
    {
        return call_user_func_array([$this, '_createRequest'], [
            'form'=>$this->createForm(MapType::class, new Map())->handleRequest($request),
            'em'=>$em,
            'controller'=>$this,
            'routeEdit'=>'admin_map_edit',
            'viewNew'=>'@admin/map/new.html.twig'
        ]);
    }
    #[Route(path: '/view/{id}', name: 'admin_map_view')]

    public function viewAction(Request $request, string $id, ParameterBagInterface $bag): Response
    {
        $cache= new MapSerializedCache($bag->get('kernel.project_dir'), $bag->get('kernel.environment'));

        return $this->render('@admin/map/view.html.twig', [
            'id'=>$id,
            'cache'=>$cache->getSerializedMap($id),
        ]);
    }

    #[Route(path: '/edit/{id}', name: 'admin_map_edit')]
    public function editAction(Request $request, Map $entity, EntityManagerInterface $em, ParameterBagInterface $bag, UrlGeneratorInterface $urlGenerator): Response
    {
        $form= $this->createForm(MapType::class, $entity)->handleRequest($request);

        $res= call_user_func_array([$this, '_updateRequest'], [
            'request'=>$request,
            'entity'=>$entity,
            'controller'=>$this,
            'form'=> $form,
            'em'=>$em,
            'view'=>'@admin/map/edit.html.twig'
        ]);

        if($res instanceof JsonResponse && $res->getStatusCode()===200){
            $cache= new MapSerializedCache($bag->get('kernel.project_dir'), $bag->get('kernel.environment'));
            $cache->updateSerializedMap($form->getData(), $urlGenerator);
        }
        return $res;
    }

    #[Route(path: '/delete/{id}', name: 'admin_map_delete')]
    public function deleteAction(Request $request, Map $entity, EntityManagerInterface $em, TranslatorInterface $translator): RedirectResponse
    {
        return call_user_func_array([$this, '_deleteRequest'], [
            'entity'=>$entity,
            'request'=>$request,
            'em'=>$em,
            'translator'=>$translator,
            'controller'=>$this,
            'routeError'=>['admin_map_edit', array('id' => $entity->getId())],
            'routeSuccess'=>'admin_map_index'
        ]);
    }
}