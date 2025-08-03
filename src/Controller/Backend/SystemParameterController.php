<?php
namespace App\Controller\Backend;
use App\Controller\BackendController;
use App\Services\Cache\FilesCache\DbStatusCache;
use App\Shared\Manager\SetupManager;
use App\Shared\tobscure\jsonapi\{Collection, Document};
use App\SystemParameter\Domain\Entity\SystemParameter;
use App\SystemParameter\Domain\Manager\SystemParameterManager;
use App\SystemParameter\Infrastructure\Serializer\SystemParameterSerializer;
use App\SystemParameter\UI\Form\SystemParameterSearchType;
use App\SystemParameter\UI\Form\SystemParameterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\{DependencyInjection\ParameterBag\ParameterBagInterface,
    Form\FormError,
    Form\FormInterface,
    HttpFoundation\JsonResponse,
    HttpFoundation\RedirectResponse,
    HttpFoundation\Request,
    HttpFoundation\Response,
    Routing\Annotation\Route,
    Routing\Generator\UrlGeneratorInterface};
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/admin/parameters')]
class SystemParameterController extends BackendController

{
    private function getCollection(array $data, UrlGeneratorInterface $urlGenerator): Collection
    {
        return (new Collection($data, new SystemParameterSerializer($urlGenerator)))
            ->fields([
                'sysparam'=>['name', 'id','country', 'language', 'organisationdbm', 'organisationsite','active'],
                'country'=>['id','name'],
                'organisationdbm'=>['id','name'],
                'organisationsite'=>['id','name']
            ])
            ->with(['country', 'admin1', 'admin2', 'admin3']);
    }

    #[Route(path: '/', name: 'admin_system_parameter_index')]
    public function index(Request $request, ParameterBagInterface $bag): Response
    {
        $localesConfig = $this->getBundleParameters()->get('locales', []) ;
        $locales= ['locales'=> array_merge($localesConfig, ['en'=>'English'])];

        $form = $this->createForm(SystemParameterSearchType::class, new SystemParameter(),$locales)
            ->handleRequest($request);

        return $this->render('@admin/system_parameter/index.html.twig',[
            'form'   => $form->createView()
        ]);
    }

    #[Route(path: '/list', name: 'admin_system_parameter_list_json')]
    public function listJsonAction(Request $request, SystemParameterManager $manager,  UrlGeneratorInterface $urlGenerator): JsonResponse
    {
        $this->acceptOnlyXmlHttpRequest($request);
        $listOptions= $this->getRequestListOptions($request);

        $form= $this->createForm(SystemParameterSearchType::class, new SystemParameter())->handleRequest($request);
        list($paginator, $data) = $manager->paginate($form->getData(), $listOptions);

        $collection = $this->getCollection($data, $urlGenerator);
        $document = (new Document($collection));
        $document->addMeta('pagination', $paginator->toArray());

        return new JsonResponse($document , 200, ['Content-Type'=>$document::MEDIA_TYPE]);
    }
    #[Route(path: '/new', name: 'admin_system_parameter_new')]
    public function newAction(Request $request, EntityManagerInterface $em): Response
    {
        $locales= ['locales'=>$this->_getBackendParameters()->get('locales', ['en'=>'English'])];
        $form= $this->createForm(SystemParameterType::class, new SystemParameter(),$locales)
            ->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            try {
                $entity = $form->getData();
                $em->persist($entity);
                $em->flush();
                $em->clear();
                return $this->redirectToRoute('admin_system_parameter_edit', array('id' => $entity->getId()));
            }catch (\Exception $ex){
                $form->addError(new FormError($ex->getMessage()));
            }
        }
        return $this->render('@admin/system_parameter/new.html.twig', ['form'=>$form->createView()]);
    }

    #[Route(path: '/edit/{id}', name: 'admin_system_parameter_edit')]
    public function editAction(Request $request, SystemParameter $entity, EntityManagerInterface $em, ParameterBagInterface $bag): Response
    {
        $locales= ['locales'=>$this->_getBackendParameters()->get('locales', ['en'=>'English'])];


        $response = $this->_updateRequest(
            $request,
            $entity,
            $this,
            $this->createForm(SystemParameterType::class, $entity, $locales)
                ->handleRequest($request),
            $em,
            '@admin/system_parameter/edit.html.twig',
            [ 'systemParameter' => $entity],
            ['onFlush' => function (EntityManagerInterface $em, FormInterface $form, SystemParameter &$entity) use ($bag) {
                $uow= $em->getUnitOfWork();
                $changeSet = $uow->getScheduledEntityUpdates();
                var_dump( !empty($changeSet));
                if (count($changeSet)) {
                    if($entity->getActive()) (new SystemParameterManager($em))->setActiveSystemParameter($entity);
                    $this->updateCache($bag, $em);
                }else{

                }
            }]
    );


//        $response=  call_user_func_array([$this, '_updateRequest'], [
//            'request'=>$request,
//            'entity'=>$entity,
//            'controller'=>$this,
//            'form'=>$this->createForm(SystemParameterType::class, $entity, $locales)
//                ->handleRequest($request),
//            'em'=>$em,
//            'view'=>'@admin/system_parameter/edit.html.twig',
//            'twigArgs'=>[ 'systemParameter' => $entity]
//        ]);

//            if($entity->getActive()) (new SystemParameterManager($em))->setActiveSystemParameter($entity);
//            $this->updateCache($bag, $em);
            return $response;
    }

//    #[Route(path: '/edit/{id}', name: 'admin_system_parameter_edit')]
//    public function editAction(Request $request, SystemParameter $sysparam, ParameterBagInterface $bag, EntityManagerInterface $em): Response
//    {
//        $caveParameters= new Arraypath($bag->get('cave'));
//        $form = $this->createForm(SystemParameterType::class, $sysparam, [
//            'locales'=>$caveParameters->get('locales', ['en'=>'English'])
//        ])->handleRequest($request);
//
//        if (!$request->isXmlHttpRequest()){
//            return $this->render('@admin/system_parameter/edit.html.twig', array(
//                'form' => $form->createView(),
//                'systemParameter' => $sysparam
//            ));;
//        }

//        if (!$form->isSubmitted() || !$form->isValid()){
//            return (new JsonErrorBag($bag->get('kernel.environment')))->addFormErrors($form)->getJsonResponse();
//        }

//        try{
//            $entity= $form->getData();
//            $em->persist($entity);
//            $em->flush();
//            $em->clear();
//            if($entity->getActive()) (new SystemParameterManager($em))->setActiveSystemParameter($entity);
//            $this->updateCache($bag, $em);
//            return new JsonResponse(null , 204);
//        }catch (\Exception $e){
//            return (new JsonErrorBag($bag->get('kernel.environment')))->addException($e, null, true)->getJsonResponse();
//        }
//
//    }

    #[Route(path: '/delete/{id}', name: 'admin_system_parameter_delete')]

    public function deleteAction(Request $request, SystemParameter $sysparam, EntityManagerInterface $em, ParameterBagInterface $bag, TranslatorInterface $translator): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete'.$sysparam->getId(), $request->get('_token'))) {
            try{
                $em->remove($sysparam);
                $em->flush();
                $this->addFlash('success', $translator->trans('msg.delete.success', [], 'cavemessages') );
                $this->updateCache($bag, $em);
            }catch(\Exception $ex){
                $this->addFlash('danger', $ex->getMessage() );
                return $this->redirectToRoute('admin_system_parameter_edit', array('id' => $sysparam->getId()));
            }
        }else{

            $this->addFlash('danger', $translator->trans('form.invalidtoken', [], 'validators'));
        }
        return $this->redirectToRoute('admin_system_parameter_index');
    }

    private function updateCache(ParameterBagInterface $bag, EntityManagerInterface $em):void
    {
        $cache = new DbStatusCache($bag->get('kernel.project_dir'), $bag->get('kernel.environment'));
        $cache->updateDataBaseStatus((new SetupManager($em))->getDataBaseStatus());
    }
}