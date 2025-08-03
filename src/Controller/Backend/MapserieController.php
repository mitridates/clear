<?php
namespace App\Controller\Backend;
use App\Controller\BackendController;
use App\Mapserie\Domain\Entity\Mapserie;
use App\Mapserie\Domain\Manager\MapSerieManager;
use App\Mapserie\Infrastructure\Serializer\MapserieSerializer;
use App\Mapserie\UI\Form\MapserieSearchType;
use App\Mapserie\UI\Form\MapserieType;
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

#[Route('/admin/mapserie')]
class MapserieController extends BackendController
{
    #[Route(path: '/', name: 'admin_mapserie_index')]
    public function indexAction(Request $request): Response
    {
        $form = $this->createForm(MapserieSearchType::class, new Mapserie());
        return $this->render('@admin/mapserie/index.html.twig',['form'   => $form->createView()]);
    }

    #[Route(path: '/list', name: 'admin_mapserie_list_json')]
    public function listJsonAction(Request $request, MapSerieManager $manager, UrlGeneratorInterface $urlGenerator,): JsonResponse
    {
        $this->acceptOnlyXmlHttpRequest($request);
        $listOptions= $this->getRequestListOptions($request);
        $form= $this->createForm(MapserieSearchType::class, new Mapserie())->handleRequest($request);
        list($paginator, $data) = $manager->paginate($form->getData(), $listOptions);
        $collection =  (new Collection($data, new MapserieSerializer($urlGenerator)))
            ->fields([
                'mapserie'=>['id','name', 'publisher', 'lengthunits', 'maptype', 'scale'],
                'organisation'=>['id','name', 'initials']
            ])
            ->with(['country', 'admin1']);
        $document = (new Document($collection));
        $document->addMeta('pagination', $paginator->toArray());
        return new JsonResponse($document , 200, ['Content-Type'=>$document::MEDIA_TYPE]);
    }

    #[Route(path: '/new', name: 'admin_mapserie_new')]
    public function newAction(Request $request, EntityManagerInterface $em): Response
    {
        $form= $this->createForm(MapserieType::class, new Mapserie())
            ->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            try {
                $entity = $form->getData();
                $em->persist($entity);
                $em->flush();
                $em->clear();
                return $this->redirectToRoute('admin_mapserie_edit', array('id' => $entity->getId()));
            }catch (\Exception $ex){
                $form->addError(new FormError($ex->getMessage()));
            }
        }
        return $this->render('@admin/mapserie/new.html.twig', ['form'=>$form->createView()]);
    }

    #[Route(path: '/edit/{id}', name: 'admin_mapserie_edit')]
    public function editAction(Request $request, Mapserie $entity, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(MapserieType::class, $entity)->handleRequest($request);

        if (!$request->isXmlHttpRequest()){
            return $this->render('@admin/mapserie/edit.html.twig', ['form' => $form->createView(), 'entity' => $entity]);
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

    #[Route(path: '/mapserie/{id}/delete', name: 'admin_mapserie_delete')]
    public function deleteAction(Request $request, Mapserie $entity, EntityManagerInterface $em, TranslatorInterface $translator): RedirectResponse
    {
        return call_user_func_array([$this, 'CommonBackendDeleteAction'], array_merge(func_get_args(), [
            'routeSuccess'=>'admin_mapserie_index',
            'routeError'=>'admin_mapserie_edit',
        ]));
    }
}