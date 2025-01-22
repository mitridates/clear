<?php
namespace App\Controller\Backend;
use App\Entity\Mapserie;
use App\Form\backend\Mapserie\MapserieSearchType;
use App\Form\backend\Mapserie\MapserieType;
use App\Manager\MapSerieManager;
use App\Utils\Json\Serializers\MapserieSerializer;
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

#[Route('/admin/mapserie')]
class BackendMapserieController extends AbstractController
{
    use BackendControllerTrait;
    #[Route(path: '/', name: 'admin_mapserie_index')]
    public function indexAction(Request $request): Response
    {
        $form = $this->createForm(MapserieSearchType::class, new Mapserie());
        return $this->render('@admin/mapserie/index.html.twig',['form'   => $form->createView()]);
    }

    #[Route(path: '/list', name: 'admin_mapserie_list_json')]
    public function listMapseriesAction(Request $request, MapSerieManager $manager, ParameterBagInterface $bag,  UrlGeneratorInterface $urlGenerator,): Response
    {
        return call_user_func_array([$this, '_listJsonRequest'], [
            'request'=>$request,
            'getCollection'=>function ($data) use ($urlGenerator){
                return (new Collection($data, new MapserieSerializer($urlGenerator)))
                    ->fields([
                        'mapserie'=>['id','name', 'publisher', 'lengthunits', 'maptype', 'scale'],
                        'organisation'=>['id','name', 'initials']
                    ])
                    ->with(['country', 'admin1'])
                    ;
            },
            'form'=>$this->createForm(MapserieSearchType::class, new Mapserie())->handleRequest($request),
            'bag'=>$bag,
            'manager'=>$manager
        ]);
    }

    #[Route(path: '/new', name: 'admin_mapserie_new')]

    public function newAction(Request $request, EntityManagerInterface $em): Response
    {
        return call_user_func_array([$this, '_createRequest'], [
            'form'=>$this->createForm(MapserieType::class, new Mapserie())->handleRequest($request),
            'em'=>$em,
            'controller'=>$this,
            'routeEdit'=>'admin_mapserie_edit',
            'viewNew'=>'@admin/mapserie/new.html.twig'
        ]);
    }

    #[Route(path: '/edit/{id}', name: 'admin_mapserie_edit')]
    public function editAction(Request $request, Mapserie $entity, EntityManagerInterface $em): Response
    {
        return call_user_func_array([$this, '_updateRequest'], [
            'request'=>$request,
            'entity'=>$entity,
            'controller'=>$this,
            'form'=> $this->createForm(MapserieType::class, $entity)->handleRequest($request),
            'em'=>$em,
            'view'=>'@admin/mapserie/edit.html.twig'
        ]);
    }

    #[Route(path: '/mapserie/{id}/delete', name: 'admin_mapserie_delete')]
    public function deleteAction(Request $request, Mapserie $entity, EntityManagerInterface $em, TranslatorInterface $translator): RedirectResponse
    {
        return call_user_func_array([$this, '_deleteRequest'], [
            'entity'=>$entity,
            'request'=>$request,
            'em'=>$em,
            'translator'=>$translator,
            'controller'=>$this,
            'routeError'=>['admin_mapserie_edit', array('id' => $entity->getId())],
            'routeSuccess'=>'admin_mapserie_index'
        ]);
    }
}