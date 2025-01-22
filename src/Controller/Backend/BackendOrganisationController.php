<?php
namespace App\Controller\Backend;
use App\Entity\Organisation;
use App\Form\backend\Organisation\OrganisationSearchType;
use App\Form\backend\Organisation\OrganisationType;
use App\Manager\OrganisationManager;
use App\Utils\Json\Serializers\OrganisationSerializer;
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

#[Route('/admin/organisation')]
class BackendOrganisationController extends AbstractController
{
    use BackendControllerTrait;

    #[Route(path: '/', name: 'admin_organisation_index')]
    public function indexAction(Request $request): Response
    {
        $form = $this->createForm(OrganisationSearchType::class, new Organisation());
        return $this->render('@admin/organisation/index.html.twig',['form'   => $form->createView()]);
    }

    #[Route(path: '/list', name: 'admin_organisation_list_json')]
    public function listOrganisationsAction(Request $request, OrganisationManager $manager,  ParameterBagInterface $bag,  UrlGeneratorInterface $urlGenerator,): Response
    {
        return call_user_func_array([$this, '_listJsonRequest'], [
            'request'=>$request,
            'getCollection'=>function ($data) use ($urlGenerator){
                return (new Collection($data, new OrganisationSerializer($urlGenerator)))
                    ->fields([
                        'organisation'=>['id','name', 'country', 'admin1', 'admin2', 'admin3'],
                        'country'=>['id','name'],
                        'admin1'=>['id','name'],
                        'admin2'=>['id','name'],
                        'admin3'=>['id','name']
                    ])
                    ->with(['country', 'admin1', 'admin2', 'admin3']);
            },
            'form'=>$this->createForm(OrganisationSearchType::class, new Organisation())->handleRequest($request),
            'bag'=>$bag,
            'manager'=>$manager
        ]);
    }

    #[Route(path: '/new', name: 'admin_organisation_new')]

    public function newAction(Request $request, EntityManagerInterface $em): Response
    {
        return call_user_func_array([$this, '_createRequest'], [
            'form'=>$this->createForm(OrganisationType::class, new Organisation())->handleRequest($request),
            'em'=>$em,
            'controller'=>$this,
            'routeEdit'=>'admin_organisation_edit',
            'viewNew'=>'@admin/organisation/new.html.twig'
        ]);
    }

    #[Route(path: '/edit/{id}', name: 'admin_organisation_edit')]
    public function editAction(Request $request, Organisation $entity, EntityManagerInterface $em): Response
    {
        return call_user_func_array([$this, '_updateRequest'], [
            'request'=>$request,
            'entity'=>$entity,
            'controller'=>$this,
            'form'=>$this->createForm(OrganisationType::class, $entity)->handleRequest($request),
            'em'=>$em,
            'view'=>'@admin/organisation/edit.html.twig'
        ]);
    }

    #[Route(path: '/organisation/{id}/delete', name: 'admin_organisation_delete')]
    public function deleteAction(Request $request, Organisation $entity, EntityManagerInterface $em, TranslatorInterface $translator): RedirectResponse
    {
        return call_user_func_array([$this, '_deleteRequest'], [
            'entity'=>$entity,
            'request'=>$request,
            'em'=>$em,
            'translator'=>$translator,
            'controller'=>$this,
            'routeError'=>['admin_organisation_edit', array('id' => $entity->getId())],
            'routeSuccess'=>'admin_organisation_index'
        ]);
    }
}