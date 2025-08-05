<?php
namespace App\Organisation\Infrastructure\Controller;
use App\Organisation\Domain\Entity\Organisation;
use App\Organisation\Domain\Manager\OrganisationManager;
use App\Organisation\Infrastructure\Serializer\OrganisationSerializer;
use App\Organisation\UI\Form\OrganisationSearchType;
use App\Organisation\UI\Form\OrganisationType;
use App\Shared\Infrastructure\Controller\BackendController;
use App\Shared\tobscure\jsonapi\Collection;
use App\Shared\tobscure\jsonapi\Document;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/admin/organisation')]
class OrganisationController extends BackendController
{
    #[Route(path: '/', name: 'admin_organisation_index')]
    public function indexAction(Request $request): Response
    {
        $form = $this->createForm(OrganisationSearchType::class, new Organisation());
        return $this->render('@admin/organisation/index.html.twig',['form'   => $form->createView()]);
    }

    #[Route(path: '/list', name: 'admin_organisation_list_json')]
    public function listJsonAction(Request $request, OrganisationManager $manager,  UrlGeneratorInterface $urlGenerator,): JsonResponse
    {
        $this->acceptOnlyXmlHttpRequest($request);
        $listOptions= $this->getRequestListOptions($request);
        $form= $this->createForm(OrganisationSearchType::class, new Organisation())->handleRequest($request);
        list($paginator, $data) = $manager->paginate($form->getData(), $listOptions);
        $collection = (new Collection($data, new OrganisationSerializer($urlGenerator)))
            ->fields([
                'organisation'=>['id','name', 'country', 'admin1', 'admin2', 'admin3'],
                'country'=>['id','name'],
                'admin1'=>['id','name'],
                'admin2'=>['id','name'],
                'admin3'=>['id','name']
            ])
            ->with(['country', 'admin1', 'admin2', 'admin3']);
        $document = (new Document($collection));
        $document->addMeta('pagination', $paginator->toArray());
        return new JsonResponse($document , 200, ['Content-Type'=>$document::MEDIA_TYPE]);

    }

    #[Route(path: '/new', name: 'admin_organisation_new')]

    public function newAction(Request $request, EntityManagerInterface $em): Response
    {
        $form= $this->createForm(OrganisationType::class, new Organisation())
            ->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            try {
                $entity = $form->getData();
                $em->persist($entity);
                $em->flush();
                $em->clear();
                return $this->redirectToRoute('admin_organisation_edit', array('id' => $entity->getId()));
            }catch (\Exception $ex){
                $form->addError(new FormError($ex->getMessage()));
            }
        }
        return $this->render('@admin/organisation/new.html.twig', ['form'=>$form->createView()]);
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