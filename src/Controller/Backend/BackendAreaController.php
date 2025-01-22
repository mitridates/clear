<?php
namespace App\Controller\Backend;
use App\Entity\Area;
use App\Form\backend\Area\AreaSearchType;
use App\Form\backend\Area\AreaType;
use App\Manager\AreaManager;
use App\Utils\Json\Serializers\AreaSerializer;
use App\vendor\tobscure\jsonapi\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/admin/area')]
class BackendAreaController extends AbstractController
{
    use BackendControllerTrait;
    #[Route(path: '/', name: 'admin_area_index')]
    public function indexAction(Request $request): Response
    {
        $form = $this->createForm(AreaSearchType::class, new Area());
        return $this->render('@admin/area/index.html.twig',['form'   => $form->createView()]);
    }

    #[Route(path: '/list', name: 'admin_area_list_json')]
    public function listAreasAction(Request $request, AreaManager $manager, ParameterBagInterface $bag,  UrlGeneratorInterface $urlGenerator,): Response
    {
        return call_user_func_array([$this, '_listJsonRequest'], [
            'request'=>$request,
            'getCollection'=>function ($data) use ($urlGenerator){
                return (new Collection($data, new AreaSerializer($urlGenerator)))
                    ->fields([
                        'area'=>['id','name','code','country', 'admin1', 'mapsheet', 'comment'],
                        'country'=>['id','name'],
                        'admin1'=>['id','name'],
                    ])
                    ->with(['country', 'admin1'])
                    ;
            },
            'form'=>$this->createForm(AreaSearchType::class, new Area())->handleRequest($request),
            'bag'=>$bag,
            'manager'=>$manager
        ]);
    }

    #[Route(path: '/new', name: 'admin_area_new')]

    public function newAction(Request $request, EntityManagerInterface $em): Response
    {
        return call_user_func_array([$this, '_createRequest'], [
            'form'=>$this->createForm(AreaType::class, new Area())->handleRequest($request),
            'em'=>$em,
            'controller'=>$this,
            'routeEdit'=>'admin_area_edit',
            'viewNew'=>'@admin/area/new.html.twig'
        ]);
    }

    #[Route(path: '/edit/{id}', name: 'admin_area_edit')]
    public function editAction(Request $request, Area $entity, EntityManagerInterface $em): Response
    {
        return call_user_func_array([$this, '_updateRequest'], [
            'request'=>$request,
            'entity'=>$entity,
            'controller'=>$this,
            'form'=> $this->createForm(AreaType::class, $entity)->handleRequest($request),
            'em'=>$em,
            'view'=>'@admin/area/edit.html.twig'
        ]);
    }

    #[Route(path: '/area/{id}/delete', name: 'admin_area_delete')]
    public function deleteAction(Request $request, Area $entity, EntityManagerInterface $em, TranslatorInterface $translator): RedirectResponse
    {
        return call_user_func_array([$this, '_deleteRequest'], [
            'entity'=>$entity,
            'request'=>$request,
            'em'=>$em,
            'translator'=>$translator,
            'controller'=>$this,
            'routeError'=>['admin_area_edit', array('id' => $entity->getId())],
            'routeSuccess'=>'admin_area_index'
        ]);
    }
}