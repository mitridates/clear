<?php

namespace App\Controller;

use App\Domain\Area\Entity\Area;
use App\Entity\Person;
use App\Shared\JsonApi\ErrorSerializer\JsonErrorBag;
use App\Shared\tobscure\jsonapi\Document;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;

class BackendController extends BaseController
{
    public function CommonBackendDeleteAction(Request $request, Area|Person $entity, EntityManagerInterface $em, TranslatorInterface $translator, string $routeSuccess, string $routeError): RedirectResponse
    {
        $tokenId= 'delete'.$entity->getId();

        if ($this->isCsrfTokenValid($tokenId,  $request->get('_token')))
        {
            try{
                $em->remove($entity);
                $em->flush();
                $this->addFlash('success', $translator->trans('msg.delete.success', [], 'cavemessages') );
            }catch(\Exception $ex){
                $this->addFlash('danger', $ex->getMessage() );
                return $this->redirectToRoute($routeError, ['id' => $entity->getId()]);
            }
        }else{
            $this->addFlash('danger', $translator->trans('form.invalidtoken', [], 'validators'));
        }
        return $this->redirectToRoute($routeSuccess);
    }

    public function CommonBackendXmlHttpRequestDeleteAction(Request $request, Object $entity, EntityManagerInterface $em, AbstractController $controller, TranslatorInterface $translator, ?string $tokenId): JsonResponse
    {
        $this->acceptOnlyXmlHttpRequest($request);

        if ($controller->isCsrfTokenValid($tokenId ?: 'delete' . $entity->getId(), $request->get('_token'))) {
            try {
                $em->remove($entity);
                $em->flush();
                return new JsonResponse(null, 200, ['Content-Type' => Document::MEDIA_TYPE]);
            } catch (\Exception $ex) {
                return (new JsonErrorBag($controller->getParameter('kernel.environment')))->addException($ex)->getJsonResponse();
            }
        } else {
            return (new JsonErrorBag($controller->getParameter('kernel.environment')))->addMsg($translator->trans('msg.token.invalidToken', [], 'cavemessages'))->getJsonResponse();
        }
    }

    public function getJsonExceptionErrorResponse(\Exception $ex): JsonResponse
    {
        $jsonError = new JsonErrorBag($this->getParameter('kernel.environment'));
        $jsonError->addException($ex, null, true);
        return $jsonError->getJsonResponse();
    }
    public function getJsonFormErrorResponse(FormInterface $form): JsonResponse
    {
        $jsonError = new JsonErrorBag($this->getParameter('kernel.environment'));
        $jsonError->addFormErrors($form);
        return $jsonError->getJsonResponse();
    }
}