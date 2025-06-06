<?php
namespace App\Controller\Backend;
use App\Controller\BackendController;
use App\Controller\BaseController;
use App\Entity\SystemParameter;
use App\Form\backend\SystemParameter\SystemParameterSearchType;
use App\Form\backend\SystemParameter\SystemParameterType;
use App\Manager\SetupManager;
use App\Manager\SystemParameterManager;
use App\Services\Cache\FilesCache\DbStatusCache;
use App\Utils\{Arraypath, Json\JsonErrorSerializer\JsonErrorBag, Json\Serializers\SystemParameterSerializer, Paginator};
use App\vendor\tobscure\jsonapi\{Collection, Document};
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\Callable_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\{DependencyInjection\ParameterBag\ParameterBagInterface,
    Form\Extension\Core\Type\FormType,
    Form\FormError,
    Form\FormInterface,
    HttpFoundation\RedirectResponse,
    HttpFoundation\Request,
    HttpFoundation\Response,
    Routing\Annotation\Route,
    Routing\Generator\UrlGeneratorInterface};
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/admin/parameters')]
class SystemParameterController extends BackendController

{
    use BackendControllerTrait;

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
    public function listSystemparameterAction(Request $request, SystemParameterManager $manager,  ParameterBagInterface $bag,  UrlGeneratorInterface $urlGenerator): Response
    {
        return call_user_func_array([$this, '_listJsonRequest'], [
            'request'=>$request,
            'getCollection'=>function ($data) use ($urlGenerator){
                return (new Collection($data, new SystemParameterSerializer($urlGenerator)))
                    ->fields([
                        'sysparam'=>['name', 'id','country', 'language', 'organisationdbm', 'organisationsite','active'],
                        'country'=>['id','name'],
                        'organisationdbm'=>['id','name'],
                        'organisationsite'=>['id','name']
                    ])
                    ->with(['country', 'admin1', 'admin2', 'admin3']);
            },
            'form'=>$this->createForm(SystemParameterSearchType::class, new SystemParameter())->handleRequest($request),
            'bag'=>$bag,
            'manager'=>$manager
        ]);
    }
    #[Route(path: '/new', name: 'admin_system_parameter_new')]
    public function newAction(Request $request, EntityManagerInterface $em): Response
    {
        $locales= ['locales'=>$this->_getBackendParameters()->get('locales', ['en'=>'English'])];
        return call_user_func_array([$this, '_createRequest'], [
            'form'=>$this->createForm(SystemParameterType::class, new SystemParameter(),$locales)
                ->handleRequest($request),
            'em'=>$em,
            'controller'=>$this,
            'routeEdit'=>'admin_system_parameter_edit',
            'viewNew'=>'@admin/parameters/edit.html.twig'
        ]);

    }
//    #[Route(path: '/new', name: 'admin_system_parameter_new')]
//    public function newAction(Request $request, ParameterBagInterface $bag, EntityManagerInterface $em): Response
//    {
//        $manager = new SystemParameterManager($em);
//        $caveParameters= new Arraypath($bag->get('cave'));
//
//        $form = $this->createForm(SystemParameterType::class, new SystemParameter(), [
//            'locales'=>$caveParameters->get('locales', ['en'=>'English'])
//
//        ])->handleRequest($request);
//
//        if($form->isSubmitted() && $form->isValid())
//        {
//            try {
//                $entity = $form->getData();
//                $em->persist($entity);
//                $em->flush();
//                $em->clear();
//                if($entity->getActive()) $manager->setActiveSystemParameter($entity);
//                $this->updateCache($bag, $em);
//                return $this->redirectToRoute('admin_system_parameter_edit', array('id' => $entity->getId()));
//            }catch (\Exception $ex){
//                    $form->addError(new FormError($ex->getMessage()));
//            }
//        }
//
//        return $this->render('@admin/system_parameter/new.html.twig',['form'   => $form->createView()]);
//    }


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