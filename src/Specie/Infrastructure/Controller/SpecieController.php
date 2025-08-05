<?php
namespace App\Specie\Infrastructure\Controller;
use App\Shared\Infrastructure\Controller\BackendController;
use App\Shared\tobscure\jsonapi\Collection;
use App\Shared\tobscure\jsonapi\Document;
use App\Specie\Domain\Entity\Specie;
use App\Specie\Domain\Manager\SpecieManager;
use App\Specie\Infrastructure\Serializer\SpecieSerializer;
use App\Specie\UI\Form\SpecieSearchType;
use App\Specie\UI\Form\SpecieType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/admin/specie')]
class SpecieController extends BackendController
{
    #[Route(path: '/', name: 'admin_specie_index')]
    public function indexAction(Request $request): Response
    {
        $form = $this->createForm(SpecieSearchType::class, new Specie());
        return $this->render('@admin/specie/index.html.twig',['form'   => $form->createView()]);
    }

    #[Route(path: '/list', name: 'admin_specie_list_json')]
    public function listJsonAction(Request $request, SpecieManager $manager,  UrlGeneratorInterface $urlGenerator): JsonResponse
    {
        $this->acceptOnlyXmlHttpRequest($request);
        $listOptions = $this->getRequestListOptions($request);
        $form = $this->createForm(SpecieSearchType::class, new Specie())->handleRequest($request);
        list($paginator, $data) = $manager->paginate($form->getData(), $listOptions);

        $collection = (new Collection($data, new SpecieSerializer($urlGenerator)))
            ->fields(['specie' => ['id', 'name', 'commonname', 'genus', 'family', 'orden', 'class', 'phylum']]);

        $document = (new Document($collection));

        $document->addMeta('pagination', $paginator->toArray());

        return new JsonResponse($document, 200, ['Content-Type' => $document::MEDIA_TYPE]);
    }

    #[Route(path: '/new', name: 'admin_specie_new')]
    public function newAction(Request $request, EntityManagerInterface $em): Response
    {
        $form= $this->createForm(SpecieType::class, new Specie())
            ->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            try {
                $entity = $form->getData();
                $em->persist($entity);
                $em->flush();
                $em->clear();
                return $this->redirectToRoute('admin_specie_edit', array('id' => $entity->getId()));
            }catch (\Exception $ex){
                $form->addError(new FormError($ex->getMessage()));
            }
        }
        return $this->render('@admin/specie/new.html.twig', ['form'=>$form->createView()]);
    }

    #[Route(path: '/edit/{id}', name: 'admin_specie_edit')]
    public function editAction(Request $request, Specie $entity, EntityManagerInterface $em): Response
    {
        return call_user_func_array([$this, '_updateRequest'], [
            'request'=>$request,
            'entity'=>$entity,
            'controller'=>$this,
            'form'=> $this->createForm(SpecieType::class, $entity)->handleRequest($request),
            'em'=>$em,
            'view'=>'@admin/specie/edit.html.twig'
        ]);
    }

    #[Route(path: '/specie/{id}/delete', name: 'admin_specie_delete')]
    public function deleteAction(Request $request, Specie $entity, EntityManagerInterface $em, TranslatorInterface $translator): RedirectResponse
    {
        return call_user_func_array([$this, '_deleteRequest'], [
            'entity'=>$entity,
            'request'=>$request,
            'em'=>$em,
            'translator'=>$translator,
            'controller'=>$this,
            'routeError'=>['admin_specie_edit', array('id' => $entity->getId())],
            'routeSuccess'=>'admin_specie_index'
        ]);
    }
}