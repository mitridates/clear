<?php

namespace App\Controller;

use App\Utils\Arraypath;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;


class BaseController extends AbstractController
{
    const BUNDLE_PARAMETERS_KEY = 'cave';

    /**
     * @param Request $request
     * @return void
     * @throws BadRequestHttpException
     */
    private function onlyXmlHttpRequest(Request $request): void
    {
        if(!$request->isXmlHttpRequest()) {
            throw new BadRequestHttpException('Not XmlHttpRequest');
        }
    }
    protected function getBundleParameters(): Arraypath
    {
        return new Arraypath($this->getParameter(self::BUNDLE_PARAMETERS_KEY));
    }

    protected function getListOptions(Request $request): array
    {
        $caveParameters= $this->getBundleParameters()->get();
        $listOptions= [
            'page'      => $request->get('page', 1),
            'ipp'       => $request->get('limit', $caveParameters->get('section:default:ipp', 20)),
            'orderBy'   =>  $request->get('orderby'),
            'sort'      => $request->get('sort'),
    }
}