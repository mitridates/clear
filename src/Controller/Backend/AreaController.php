<?php
namespace App\Controller\Backend;
use App\Controller\BackendController;
use App\Domain\Area\Entity\Area;
use App\Domain\Area\Manager\AreaManager;
use App\Domain\JsonApi\Serializers\AreaSerializer;
use App\Form\backend\Area\AreaSearchType;
use App\Form\backend\Area\AreaType;
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

#[Route('/admin/area')]
class AreaController extends BackendController
{
    #[Route(path: '/', name: 'admin_area_index')]
    public function indexAction(Request $request): Response
    {
        $form = $this->createForm(AreaSearchType::class, new Area());
        return $this->render('@admin/area/index.html.twig',['form'   => $form->createView()]);
    }

    #[Route(path: '/list', name: 'admin_area_list_json')]
    public function listJsonAction(Request $request, AreaManager $manager,   UrlGeneratorInterface $urlGenerator,): JsonResponse
    {
        $this->acceptOnlyXmlHttpRequest($request);
        $listOptions= $this->getRequestListOptions($request);
        $form= $this->createForm(AreaSearchType::class, new Area())->handleRequest($request);
        list($paginator, $data) = $manager->paginate($form->getData(), $listOptions);
        $collection = (new Collection($data, new AreaSerializer($urlGenerator)))
            ->fields([
                'area'=>['id','name','code','country', 'admin1', 'mapsheet', 'comment'],
                'country'=>['id','name'],
                'admin1'=>['id','name'],
            ])
            ->with(['country', 'admin1']);
        $document = (new Document($collection));
        $document->addMeta('pagination', $paginator->toArray());
        return new JsonResponse($document , 200, ['Content-Type'=>$document::MEDIA_TYPE]);

    }

    #[Route(path: '/new', name: 'admin_area_new')]
    public function newAction(Request $request, EntityManagerInterface $em): Response
    {
        $form= $this->createForm(AreaType::class, new Area())
            ->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            try {
                $entity = $form->getData();
                $em->persist($entity);
                $em->flush();
                $em->clear();
                return $this->redirectToRoute('admin_area_edit', array('id' => $entity->getId()));
            }catch (\Exception $ex){
                $form->addError(new FormError($ex->getMessage()));
            }
        }
        return $this->render('@admin/area/new.html.twig', ['form'=>$form->createView()]);
    }

    #[Route(path: '/edit/{id}', name: 'admin_area_edit')]
    public function editAction(Request $request, Area $entity, EntityManagerInterface $em): Response
    {
        $form= $this->createForm(AreaType::class, $entity)->handleRequest($request);

        if (!$request->isXmlHttpRequest()){
            return $this->render('@admin/area/edit.html.twig', ['form' => $form->createView(), 'entity' => $entity]);
        }

        if (!$form->isSubmitted() || !$form->isValid()){
            $this->getJsonFormErrorResponse($form);
        }

        try{
            $em->persist($form->getData());
            $em->flush();
            return new JsonResponse(null , 200);
        }catch (\Exception $e){
            return $this->getJsonExceptionErrorResponse($e);
        }
    }

    #[Route(path: '/area/{id}/delete', name: 'admin_area_delete')]
    public function deleteAction(Request $request, Area $entity, EntityManagerInterface $em, TranslatorInterface $translator): RedirectResponse
    {
        return call_user_func_array([$this, 'CommonBackendDeleteAction'], array_merge(func_get_args(), [
            'routeSuccess'=>'admin_area_index',
            'routeError'=>'admin_area_edit',
        ]));
    }
}