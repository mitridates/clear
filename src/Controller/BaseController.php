<?php

namespace App\Controller;

use App\Shared\Arraypath;
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
    protected function acceptOnlyXmlHttpRequest(Request $request): self
    {
        if(!$request->isXmlHttpRequest()) {
            throw new BadRequestHttpException('Not XmlHttpRequest');
        }
        return $this;
    }

    /**
     * @return Arraypath
     * @throws \Exception
     */
    protected function getBundleParameters(): Arraypath
    {
        if(!$this->getParameter(self::BUNDLE_PARAMETERS_KEY)){
            throw new \Exception(sprintf('Bundle key  "%" not found', self::BUNDLE_PARAMETERS_KEY));
        }
        return new Arraypath($this->getParameter(self::BUNDLE_PARAMETERS_KEY));
    }

    /**
     * Obtiene las opciones de listado combinando valores por defecto,
     * configuración por sección y parámetros del request.
     *
     * Orden de prioridad:
     * 1. Defaults
     * 2. Configuración del bundle por sección (si existe)
     * 3. Parámetros del request (sobrescriben si están presentes)
     *
     * Convierte automáticamente a entero los valores de 'page' y 'limit'.
     *
     * @param Request $request La petición HTTP
     * @param string|null $section Sección opcional en configuración
     * @return array  Array con las opciones finales de listado
     * @throws \Exception
     */
    protected function getRequestListOptions(Request $request, ?string $section='default'): array
    {

        $listConfig = $this->getBundleParameters()->get('listOptions', []);
        $defaults= [
            'page'=>1,
            'limit'=>25,// perPage/ipp → limit
            'order'=>'id',
            'sort'=>'desc',
        ];

        $opt= array_merge(
            $defaults,
            array_key_exists($section, $listConfig) ?  $listConfig[$section] : []
        );

        // Mapeo de alias => clave real
        $aliases = [
            'orderBy' => 'order',
            'perPage' => 'limit',
            'ipp' => 'limit',
        ];

        // Aplicar alias si vienen en el request
        foreach ($aliases as $alias => $actual) {
            $aliasVal = $request->get($alias);
            if ($aliasVal !== null) {
                $opt[$actual] = in_array($actual, ['page', 'limit']) ? (int) $aliasVal : $aliasVal;
            }
        }
        // Obtener valores del request
        foreach (array_keys($opt) as $k) {
            $reqVal = $request->get($k);
            if ($reqVal !== null) {
                if (in_array($k, ['page', 'limit'])) {
                    $opt[$k] = (int) $reqVal;
                } else {
                    $opt[$k] = $reqVal;
                }
            }
        }
        return $opt;
    }


}