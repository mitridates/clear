<?php

namespace App\Controller\Backend;

use App\Manager\AbstractManager;
use App\Utils\Arraypath;
use App\Utils\Json\JsonErrorSerializer\JsonErrorBag;
use App\vendor\tobscure\jsonapi\Document;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Contracts\Translation\TranslatorInterface;

trait BackendControllerTrait
{


    private function _getBackendParameters(): Arraypath
    {
        return new Arraypath($this->getParameter('cave'));
    }
    public function _createRequest(FormInterface $form, EntityManagerInterface $em, AbstractController $controller,string $routeEdit, string $viewNew, array $twigArgs=[]): Response
    {

        if($form->isSubmitted() && $form->isValid())
        {
            try {
                $entity = $form->getData();
                $em->persist($entity);
                $em->flush();
                $em->clear();
                return $controller->redirectToRoute($routeEdit, array('id' => $entity->getId()));
            }catch (\Exception $ex){
                $form->addError(new FormError($ex->getMessage()));
            }
        }
        $twigArgs['form']=$form->createView();
        return $controller->render($viewNew, $twigArgs);
    }

    /**
     * @param Request $request
     * @param Object $entity
     * @param AbstractController $controller
     * @param FormInterface $form
     * @param EntityManagerInterface $em
     * @param string $view
     * @param array $twigArgs
     * @param array $callBack
     * @return Response
     */
    public function _updateRequest(Request $request, Object $entity, AbstractController $controller, FormInterface $form, EntityManagerInterface $em, string $view, array $twigArgs=[], ?array $callBack=[]): Response
    {

        if (!$request->isXmlHttpRequest()){
            return $controller->render($view, array_merge($twigArgs, ['form' => $form->createView(), 'entity' => $entity]));
        }

        if (!$form->isSubmitted() || !$form->isValid()){
            return (new JsonErrorBag($controller->getParameter('kernel.environment')))->addFormErrors($form)->getJsonResponse();
        }

        try{
            $em->persist($form->getData());
            if(isset($callBack['onFlush'])) $callBack['onFlush']($em, $form, $entity);
            $em->flush();
            $em->clear();
            return new JsonResponse(null , 200);
        }catch (\Exception $e){
            return (new JsonErrorBag($controller->getParameter('kernel.environment')))->addException($e, null, true)->getJsonResponse();
        }
    }

    public function _listJsonRequest(
        Request $request,
        \Closure $getCollection,
        ?FormInterface $form,
        ParameterBagInterface $bag,
        AbstractManager $manager,
        null|object $entity=null,
        array $options=[]
    ): JsonResponse
    {
     
        $this->onlyXmlHttpRequest($request);
        
        $caveParameters= new Arraypath($bag->get('cave'));
        $listOptions= [
            'page'      => $request->get('page', 1),
            'ipp'       => $request->get('limit', $caveParameters->get('section:default:ipp', 20)),
            'orderBy'   =>  $request->get('orderby'),
            'sort'      => $request->get('sort'),
        ];


        if(!$entity)
        {
            if(!$form->isSubmitted() || !$form->isValid()){
                return (new JsonErrorBag($this->getParameter('kernel.environment')))->addFormErrors($form)->getJsonResponse();
            }
            $entity = $form->getData();
        }
        list($paginator, $data) = (!isset($options['paginate']) || !is_callable($options['paginate']) ) ?
            $manager->paginate($entity, $listOptions) :
            $options['paginate']($entity, $listOptions);

        $collection = $getCollection($data);
        $document = (new Document($collection));
        $document->addMeta('pagination', $paginator->toArray());
        return new JsonResponse($document , 200, ['Content-Type'=>$document::MEDIA_TYPE]);
    }

    public function _deleteRequest(Request $request, Object $entity, EntityManagerInterface $em, AbstractController $controller, TranslatorInterface $translator, string|array $routeError, string|array $routeSuccess, ?string $tokenId): RedirectResponse
    {
        if ($controller->isCsrfTokenValid($tokenId? $tokenId : 'delete'.$entity->getId(), $request->get('_token')))
        {
            try{
                $em->remove($entity);
                $em->flush();
                $controller->addFlash('success', $translator->trans('msg.delete.success', [], 'cavemessages') );
            }catch(\Exception $ex){
                $this->addFlash('danger', $ex->getMessage() );
                return $controller->redirectToRoute($routeError);
            }
        }else{
            $controller->addFlash('danger', $translator->trans('form.invalidtoken', [], 'validators'));
        }
        return $controller->redirectToRoute($routeSuccess);
    }

    public function _deleteXmlHttpRequest(Request $request, Object $entity, EntityManagerInterface $em, AbstractController $controller, TranslatorInterface $translator, string|array $routeError, string|array $routeSuccess, ?string $tokenId): JsonResponse
    {
        $this->onlyXmlHttpRequest($request);
        
        if ($controller->isCsrfTokenValid($tokenId?: 'delete'.$entity->getId(), $request->get('_token')))
        {
            try{
                $em->remove($entity);
                $em->flush();
                return new JsonResponse(null , 200, ['Content-Type'=>Document::MEDIA_TYPE]);
            }catch(\Exception $ex){
                return (new JsonErrorBag($controller->getParameter('kernel.environment')))->addException($ex)->getJsonResponse();
            }
        }else{
            return (new JsonErrorBag($controller->getParameter('kernel.environment')))->addMsg( $translator->trans('msg.token.invalidToken', [], 'cavemessages'))->getJsonResponse();
        }
    }
}