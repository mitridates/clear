<?php

namespace App\Shared\Infrastructure\Controller\Json;

use App\Shared\JsonApi\ErrorSerializer\JsonErrorBag;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/locale')]
class JsonLocaleSubscriberController extends AbstractController
{
    /**
     * Set _locale parameter in request for LocaleSubscriber
     */
    #[Route(path: '/_locale/{_locale}', name: 'backend_locale_json_subscriber', methods: ['POST'])]

    public function localejsonAction(Request $request): JsonResponse
    {
        $_locale = $request->get('_locale');
        $sessLocale= $request->getSession()->get('_locale');

        if($sessLocale === $_locale){
            return new JsonResponse(['locale'=> $sessLocale]);
        }else{
            $err_bag= new JsonErrorBag($this->getParameter('kernel.environment'));
            return $err_bag->addException(
                new \Exception(sprintf('No se pudo modificar Locale. Session: %s != Request: %s', $sessLocale, $_locale ))
            )->getJsonResponse();
        }
    }
}