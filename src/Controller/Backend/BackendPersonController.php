<?php
namespace App\Controller\Backend;
use App\Entity\Person;
use App\Form\backend\Person\PersonSearchType;
use App\Form\backend\Person\PersonType;
use App\Manager\PersonManager;
use App\Utils\Json\Serializers\PersonSerializer;
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

#[Route('/admin/person')]
class BackendPersonController extends AbstractController
{
    use BackendControllerTrait;
    #[Route(path: '/', name: 'admin_person_index')]
    public function indexAction(Request $request): Response
    {
        $form = $this->createForm(PersonSearchType::class, new Person());
        return $this->render('@admin/person/index.html.twig',['form'   => $form->createView()]);
    }

    #[Route(path: '/list', name: 'admin_person_list_json')]
    public function listPersonsAction(Request $request, PersonManager $manager, ParameterBagInterface $bag,  UrlGeneratorInterface $urlGenerator,): Response
    {
        return call_user_func_array([$this, '_listJsonRequest'], [
            'request'=>$request,
            'getCollection'=>function ($data) use ($urlGenerator){
                return (new Collection($data, new PersonSerializer($urlGenerator)))
                    ->fields([
                        'person'=>['id','name', 'surname','code','country', 'admin1', 'mapsheet', 'comment'],
                        'country'=>['id','name'],
                        'admin1'=>['id','name'],
                    ])
                    ->with(['country', 'admin1'])
                    ;
            },
            'form'=>$this->createForm(PersonSearchType::class, new Person())->handleRequest($request),
            'bag'=>$bag,
            'manager'=>$manager
        ]);
    }

    #[Route(path: '/new', name: 'admin_person_new')]

    public function newAction(Request $request, EntityManagerInterface $em): Response
    {
        return call_user_func_array([$this, '_createRequest'], [
            'form'=>$this->createForm(PersonType::class, new Person())->handleRequest($request),
            'em'=>$em,
            'controller'=>$this,
            'routeEdit'=>'admin_person_edit',
            'viewNew'=>'@admin/person/new.html.twig'
        ]);
    }

    #[Route(path: '/edit/{id}', name: 'admin_person_edit')]
    public function editAction(Request $request, Person $entity, EntityManagerInterface $em): Response
    {
        return call_user_func_array([$this, '_updateRequest'], [
            'request'=>$request,
            'entity'=>$entity,
            'controller'=>$this,
            'form'=> $this->createForm(PersonType::class, $entity)->handleRequest($request),
            'em'=>$em,
            'view'=>'@admin/person/edit.html.twig'
        ]);
    }

    #[Route(path: '/person/{id}/delete', name: 'admin_person_delete')]
    public function deleteAction(Request $request, Person $entity, EntityManagerInterface $em, TranslatorInterface $translator): RedirectResponse
    {
        return call_user_func_array([$this, '_deleteRequest'], [
            'entity'=>$entity,
            'request'=>$request,
            'em'=>$em,
            'translator'=>$translator,
            'controller'=>$this,
            'routeError'=>['admin_person_edit', array('id' => $entity->getId())],
            'routeSuccess'=>'admin_person_index'
        ]);
    }
}