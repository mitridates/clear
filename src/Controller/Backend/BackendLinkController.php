<?php
namespace App\Controller\Backend;
use App\Entity\Link;
use App\Form\backend\Link\LinkSearchType;
use App\Form\backend\Link\LinkType;
use App\Manager\LinkManager;
use App\Utils\Json\Serializers\LinkSerializer;
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

#[Route('/admin/link')]
class BackendLinkController extends AbstractController
{
    use BackendControllerTrait;
    #[Route(path: '/', name: 'admin_link_index')]
    public function indexAction(Request $request): Response
    {
        $form = $this->createForm(LinkSearchType::class, new Link());
        return $this->render('@admin/link/index.html.twig',['form'   => $form->createView()]);
    }

    #[Route(path: '/list', name: 'admin_link_list_json')]
    public function listLinksAction(Request $request, LinkManager $manager, ParameterBagInterface $bag,  UrlGeneratorInterface $urlGenerator,): Response
    {
        return call_user_func_array([$this, '_listJsonRequest'], [
            'request'=>$request,
            'getCollection'=>function ($data) use ($urlGenerator){
                return (new Collection($data, new LinkSerializer($urlGenerator)))
                    ->fields([
                        'link'=>['id','title','author','organisation', 'authorname','organisationid', 'url', 'mime', 'accessed'],
                        'author'=>['id','name'],
                        'organisation'=>['id','name', 'initials']
                    ])
                    ->with(['author', 'organisation']);
            },
            'form'=>$this->createForm(LinkSearchType::class, new Link())->handleRequest($request),
            'bag'=>$bag,
            'manager'=>$manager
        ]);
    }

    #[Route(path: '/new', name: 'admin_link_new')]

    public function newAction(Request $request, EntityManagerInterface $em): Response
    {
        return call_user_func_array([$this, '_createRequest'], [
            'form'=>$this->createForm(LinkType::class, new Link())->handleRequest($request),
            'em'=>$em,
            'controller'=>$this,
            'routeEdit'=>'admin_link_edit',
            'viewNew'=>'@admin/link/new.html.twig'
        ]);
    }

    #[Route(path: '/edit/{id}', name: 'admin_link_edit')]
    public function editAction(Request $request, Link $entity, EntityManagerInterface $em): Response
    {
        return call_user_func_array([$this, '_updateRequest'], [
            'request'=>$request,
            'entity'=>$entity,
            'controller'=>$this,
            'form'=> $this->createForm(LinkType::class, $entity)->handleRequest($request),
            'em'=>$em,
            'view'=>'@admin/link/edit.html.twig'
        ]);
    }

    #[Route(path: '/link/{id}/delete', name: 'admin_link_delete')]
    public function deleteAction(Request $request, Link $entity, EntityManagerInterface $em, TranslatorInterface $translator): RedirectResponse
    {
        return call_user_func_array([$this, '_deleteRequest'], [
            'entity'=>$entity,
            'request'=>$request,
            'em'=>$em,
            'translator'=>$translator,
            'controller'=>$this,
            'routeError'=>['admin_link_edit', array('id' => $entity->getId())],
            'routeSuccess'=>'admin_link_index'
        ]);
    }
}