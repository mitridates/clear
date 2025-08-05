<?php
namespace App\Map\Infrastructure\Controller;
use App\Map\Domain\Entity\Map\Map;
use App\Map\Domain\Serialization\MapSerializerRegistry;
use App\Map\Infrastructure\Serializer\MapSerializer;
use App\Map\UI\Form\MapPartialCoordinatesType;
use App\Map\UI\Form\MapPartialSourceType;
use App\Map\UI\Form\MapPartialSurveyType;
use App\Map\UI\Form\Model\PartialFormTypeInterface;
use App\Services\Cache\FilesCache\Map\MapSerializedCache;
use App\Shared\Infrastructure\Controller\BackendController;
use App\Shared\reflection\EntityReflectionHelper;
use App\Shared\tobscure\jsonapi\Document;
use App\Shared\tobscure\jsonapi\Resource;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/admin/map/partial')]
class MapPartialController extends BackendController
{

    private function getFormType($type): string
    {
        return match ($type) {
            'coordinates'=> MapPartialCoordinatesType::class,
            'survey'=> MapPartialSurveyType::class,
            'source'=> MapPartialSourceType::class,
            default => throw new \InvalidArgumentException("No existe formType para '{$type}'.")
        };
    }

    #[Route(path: '/edit/{relationship}/{id}', name: 'admin_map_partial_edit')]
    public function editRelationshipAction(Request $request, Map $map, string $relationship, EntityManagerInterface $em, UrlGeneratorInterface $urlGenerator): Response
    {
        $classType= $this->getFormType($relationship);// MapSerializerRegistry::PARTIAL_FORM_TYPE[$relationship];
        $form= $this->createForm($classType, $map)->handleRequest($request);
        $twigArgs=[
            'entity' => $map,
            'relationship'=>$relationship,
            'relType'=>'partial',
            'form'=>$form->createView(),
        ];
        if (!$request->isXmlHttpRequest()){
            return $this->render('@admin/map/edit.html.twig', $twigArgs);
        }
        if (!$form->isSubmitted() || !$form->isValid())
        {
            return $this->getJsonFormErrorResponse($form);
        }

        try{

            $entity= $form->getData();
            $em->persist($entity);
            $em->flush();
            $em->clear();
            //El formulario es un subconjunto de Map y por tanto actualizamos la cache de  Map
            $this->updateCache($entity, $urlGenerator);
            return new JsonResponse(null , 200);

        }catch (\Exception $e){
            return $this->getJsonExceptionErrorResponse($e);
        }
    }
    
    

    #[Route(path: '/delete/{relationship}/{id}', name: 'admin_map_partial_delete',
        requirements: ['id' => '\w+', 'relationship' => '(\w+)'])]
    public function deletePartialAction(Request $request, Map $entity, string $relationship,  EntityManagerInterface $em, TranslatorInterface $translator, UrlGeneratorInterface $urlGenerator): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete_partial_'.$relationship.'_'.$entity->getId(), $request->get('_token')))
        {
            /**@var PartialFormTypeInterface $type */
            $type= $this->getFormType($relationship);
            EntityReflectionHelper::setNullProperties($entity, $type::getFieldNames());
            $em->persist($entity);
            $em->flush();
            $em->clear();
            $this->updateCache($entity, $urlGenerator);
        }else{
            $this->addFlash('danger', $translator->trans('form.invalidtoken', [], 'validators'));
        }
        return $this->redirectToRoute('admin_map_partial_edit', ['id'=>$entity->getId(), 'relationship'=>$relationship]);

    }
    private function  serializeMap(Map $map, UrlGeneratorInterface $urlGenerator): Document
    {
        /*******SERIALIZE MAP*******/
        $serializer= new MapSerializer($urlGenerator);
        $resource = new Resource($map, $serializer);
        $resource->with(
            MapSerializerRegistry::MAP_SERIALIZER_FIELDS['with']
        )->fields(
            MapSerializerRegistry::MAP_SERIALIZER_FIELDS['fields']
        )
        ;
        return new Document($resource);
    }

    private function updateCache(Map $map, UrlGeneratorInterface $urlGenerator): void
    {
        $project_dir= $this->getParameter('kernel.project_dir');
        $project_env= $this->getParameter('kernel.environment');
        $cache= new MapSerializedCache($project_dir, $project_env);
        $document= $this->serializeMap($map, $urlGenerator);
        $cache->updateSerializedMap($map, $document);
    }
}