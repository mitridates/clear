<?php
namespace App\Controller\Backend;
use App\Entity\SystemParameter;
use App\Form\backend\SystemParameter\SystemParameterSearchType;
use App\Form\backend\SystemParameter\SystemParameterType;
use App\Manager\SetupManager;
use App\Manager\SystemParameterManager;
use App\Services\Cache\FilesCache\DbStatusCache;
use App\Utils\{Arraypath, Json\JsonErrorSerializer\JsonErrorBag, Json\Serializers\SystemParameterSerializer, Paginator};
use App\vendor\tobscure\jsonapi\{Collection, Document};
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\{DependencyInjection\ParameterBag\ParameterBagInterface,
    Form\FormError,
    HttpFoundation\JsonResponse,
    HttpFoundation\RedirectResponse,
    HttpFoundation\Request,
    HttpFoundation\Response,
    HttpKernel\Exception\BadRequestHttpException,
    Routing\Annotation\Route,
    Routing\Generator\UrlGeneratorInterface};
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/admin/parameters')]
class BackendSystemParameterController extends AbstractController
{

    #[Route(path: '/', name: 'admin_system_parameter_index')]
    public function indexAction(Request $request, ParameterBagInterface $bag): Response
    {
        $caveParameters= new Arraypath($bag->get('cave'));
        $form = $this->createForm(SystemParameterSearchType::class, new SystemParameter(), [
          'locales'=>$caveParameters->get('locales', ['en'=>'English'])
      ])->handleRequest($request);
        return $this->render('@admin/system_parameter/index.html.twig',['form'   => $form->createView()]);
    }


    #[Route(path: '/list', name: 'admin_system_parameter_index_list_json')]
    public function indexListJsonAction(Request $request, SystemParameterManager $manager, UrlGeneratorInterface $urlGenerator, ParameterBagInterface $bag): JsonResponse
    {

        if(!$request->isXmlHttpRequest()) {
            throw new BadRequestHttpException('Not XmlHttpRequest');
        }

        $caveParameters= new Arraypath($bag->get('cave'));
        $form = $this->createForm(SystemParameterSearchType::class, new SystemParameter(), [
            'locales'=>$caveParameters->get('locales', ['en'=>'English'])
            ]
        )->handleRequest($request);

        $listOptions= [
            'page'      => $request->get('page', 1),
            'ipp'       => $request->get('limit', $caveParameters->get('section:default:ipp', 20)),
            'orderBy'   =>  $request->get('orderby'),
            'sort'      => $request->get('sort'),
        ];


        if(!$form->isSubmitted() || !$form->isValid()){
            return (new JsonErrorBag($this->getParameter('kernel.environment')))->addFormErrors($form)->getJsonResponse();
        }

        /**
         * @var SystemParameter $entity
         * @var Paginator $paginator
         * @var SystemParameter[] $data
         */
        $entity = $form->getData();
        list($paginator, $data) = $manager->paginate($entity, $listOptions);

        $collection = (new Collection($data, new SystemParameterSerializer($urlGenerator)))
            ->fields([
                'sysparam'=>['name', 'id','country', 'language', 'organisationdbm', 'organisationsite','active'],
                'country'=>['id','name'],
                'organisationdbm'=>['id','name'],
                'organisationsite'=>['id','name']
            ])
            ->with(['country', 'organisationdbm', 'organisationsite']);

        $document = (new Document($collection));
        $document->addMeta('pagination', $paginator->toArray());

        return new JsonResponse($document , 200, ['Content-Type'=>$document::MEDIA_TYPE]);
    }

    #[Route(path: '/new', name: 'admin_system_parameter_new')]
    public function newAction(Request $request, ParameterBagInterface $bag, EntityManagerInterface $em): Response
    {
        $manager = new SystemParameterManager($em);
        $caveParameters= new Arraypath($bag->get('cave'));

        $form = $this->createForm(SystemParameterType::class, new SystemParameter(), [
            'locales'=>$caveParameters->get('locales', ['en'=>'English'])

        ])->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            try {
                $entity = $form->getData();
                $em->persist($entity);
                $em->flush();
                $em->clear();
                if($entity->getActive()) $manager->setActiveSystemParameter($entity);
                $this->updateCache($bag, $em);
                return $this->redirectToRoute('admin_system_parameter_edit', array('id' => $entity->getId()));
            }catch (\Exception $ex){
                    $form->addError(new FormError($ex->getMessage()));
            }
        }

        return $this->render('@admin/system_parameter/new.html.twig',['form'   => $form->createView()]);
    }

    #[Route(path: '/edit/{id}', name: 'admin_system_parameter_edit')]
    public function editAction(Request $request, SystemParameter $sysparam, ParameterBagInterface $bag, EntityManagerInterface $em): Response
    {
        $caveParameters= new Arraypath($bag->get('cave'));
        $form = $this->createForm(SystemParameterType::class, $sysparam, [
            'locales'=>$caveParameters->get('locales', ['en'=>'English'])
        ])->handleRequest($request);

        if (!$request->isXmlHttpRequest()){
            return $this->render('@admin/system_parameter/edit.html.twig', array(
                'form' => $form->createView(),
                'systemParameter' => $sysparam
            ));;
        }

        if (!$form->isSubmitted() || !$form->isValid()){
            return (new JsonErrorBag($bag->get('kernel.environment')))->addFormErrors($form)->getJsonResponse();
        }

        try{
            $entity= $form->getData();
            $em->persist($entity);
            $em->flush();
            $em->clear();
            if($entity->getActive()) (new SystemParameterManager($em))->setActiveSystemParameter($entity);
            $this->updateCache($bag, $em);
            return new JsonResponse(null , 204);
        }catch (\Exception $e){
            return (new JsonErrorBag($bag->get('kernel.environment')))->addException($e, null, true)->getJsonResponse();
        }

    }

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

    private function updateCache(ParameterBagInterface $bag, EntityManagerInterface $em)
    {
        $cache = new DbStatusCache($bag->get('kernel.project_dir'), $bag->get('kernel.environment'));
        $cache->updateDataBaseStatus((new SetupManager($em))->getDataBaseStatus());
    }
}