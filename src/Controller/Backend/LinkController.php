<?php
namespace App\Controller\Backend;
use App\Controller\BackendController;
use App\Domain\JsonApi\Serializers\LinkSerializer;
use App\Entity\Link;
use App\Form\backend\Link\LinkSearchType;
use App\Form\backend\Link\LinkType;
use App\Manager\LinkManager;
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

#[Route('/admin/link')]
class LinkController extends BackendController
{
    #[Route(path: '/', name: 'admin_link_index')]
    public function indexAction(Request $request): Response
    {
        $form = $this->createForm(LinkSearchType::class, new Link());
        return $this->render('@admin/link/index.html.twig',['form'   => $form->createView()]);
    }

    #[Route(path: '/list', name: 'admin_link_list_json')]
    public function listJsonAction(Request $request, LinkManager $manager,  UrlGeneratorInterface $urlGenerator): JsonResponse
    {
        $this->acceptOnlyXmlHttpRequest($request);
        $listOptions= $this->getRequestListOptions($request);
        $form= $this->createForm(LinkSearchType::class, new Link())->handleRequest($request);
        list($paginator, $data) = $manager->paginate($form->getData(), $listOptions);
        $collection = (new Collection($data, new LinkSerializer($urlGenerator)))
            ->fields([
                'link'=>['id','title','author','organisation', 'authorname','organisationid', 'url', 'mime', 'accessed'],
                'author'=>['id','name'],
                'organisation'=>['id','name', 'initials']
            ])
            ->with(['author', 'organisation']);
        $document = (new Document($collection));
        $document->addMeta('pagination', $paginator->toArray());
        return new JsonResponse($document , 200, ['Content-Type'=>$document::MEDIA_TYPE]);
    }

    #[Route(path: '/new', name: 'admin_link_new')]

    public function newAction(Request $request, EntityManagerInterface $em): Response
    {
        $form= $this->createForm(LinkType::class, new Link())
            ->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            try {
                $entity = $form->getData();
                $em->persist($entity);
                $em->flush();
                $em->clear();
                return $this->redirectToRoute('admin_link_edit', array('id' => $entity->getId()));
            }catch (\Exception $ex){
                $form->addError(new FormError($ex->getMessage()));
            }
        }
        return $this->render('@admin/link/new.html.twig', ['form'=>$form->createView()]);
    }
    #[Route(path: '/edit/{id}', name: 'admin_link_edit')]
    public function editAction(Request $request, Link $entity, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(LinkType::class, $entity)->handleRequest($request);

        if (!$request->isXmlHttpRequest()){
            return $this->render('@admin/link/edit.html.twig', ['form' => $form->createView(), 'entity' => $entity]);
        }

        if (!$form->isSubmitted() || !$form->isValid())
        {
            return $this->getJsonFormErrorResponse($form);
        }

         try{
             $em->persist($form->getData());
             $em->flush();
             $em->clear();
             return new JsonResponse(null , 200);
         }catch (\Exception $e){
             return $this->getJsonExceptionErrorResponse($e);
         }
    }

    #[Route(path: '/link/{id}/delete', name: 'admin_link_delete')]
    public function deleteAction(Request $request, Link $entity, EntityManagerInterface $em, TranslatorInterface $translator): RedirectResponse
    {
        return call_user_func_array([$this, 'CommonBackendDeleteAction'], array_merge(func_get_args(), [
            'routeSuccess'=>'admin_link_index',
            'routeError'=>'admin_link_edit',
        ]));
    }
}