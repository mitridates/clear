<?php
namespace App\Person\Infrastructure\Controller;
use App\Person\Domain\Entity\Person;
use App\Person\Domain\Manager\PersonManager;
use App\Person\Infrastructure\Serializer\PersonSerializer;
use App\Person\UI\Form\PersonSearchType;
use App\Person\UI\Form\PersonType;
use App\Shared\Infrastructure\Controller\BackendController;
use App\Shared\tobscure\jsonapi\Collection;
use App\Shared\tobscure\jsonapi\Document;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/admin/person')]
class PersonController extends BackendController
{
    #[Route(path: '/', name: 'admin_person_index')]
    public function indexAction(Request $request): Response
    {
        $form = $this->createForm(PersonSearchType::class, new Person());
        return $this->render('@admin/person/index.html.twig',['form'   => $form->createView()]);
    }

    /**
     * @throws \Exception
     */
    #[Route(path: '/list', name: 'admin_person_list_json')]
    public function listJsonAction(Request $request, PersonManager $manager, ParameterBagInterface $bag,  UrlGeneratorInterface $urlGenerator,): JsonResponse
    {
        $this->acceptOnlyXmlHttpRequest($request);
        $listOptions= $this->getRequestListOptions($request);
        $form= $this->createForm(PersonSearchType::class, new Person())->handleRequest($request);
        list($paginator, $data) = $manager->paginate($form->getData(), $listOptions);
        $collection = (new Collection($data, new PersonSerializer($urlGenerator)))
            ->fields(['citation'=>['id','title','subtitle','jsondata']]);
        $document = (new Document($collection));
        $document->addMeta('pagination', $paginator->toArray());
        return new JsonResponse($document , 200, ['Content-Type'=>$document::MEDIA_TYPE]);
    }

    #[Route(path: '/new', name: 'admin_person_new')]

    public function newAction(Request $request, EntityManagerInterface $em): Response
    {
        $form= $this->createForm(PersonType::class, new Person())
            ->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            try {
                $entity = $form->getData();
                $em->persist($entity);
                $em->flush();
                $em->clear();
                return $this->redirectToRoute('admin_person_edit', array('id' => $entity->getId()));
            }catch (\Exception $ex){
                $form->addError(new FormError($ex->getMessage()));
            }
        }
        return $this->render('@admin/person/new.html.twig', ['form'=>$form->createView()]);
    }

    #[Route(path: '/edit/{id}', name: 'admin_person_edit')]
    public function editAction(Request $request, Person $entity, EntityManagerInterface $em): Response
    {
        $form= $this->createForm(PersonType::class, $entity)->handleRequest($request);
        if (!$request->isXmlHttpRequest()){
            return $this->render('@admin/person/edit.html.twig', ['form' => $form->createView(), 'entity' => $entity]);
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

    #[Route(path: '/person/{id}/delete', name: 'admin_person_delete')]
    public function deleteAction(Request $request, Person $entity, EntityManagerInterface $em, TranslatorInterface $translator): RedirectResponse
    {
        return call_user_func_array([$this, 'CommonBackendDeleteAction'], array_merge(func_get_args(), [
            'routeSuccess'=>'admin_person_index',
            'routeError'=>'admin_person_edit',
        ]));

    }
}