<?php
namespace App\Controller\Backend;
use App\Controller\BackendController;
use App\Entity\Map\Map;
use App\Entity\Map\Mapcomment;
use App\Entity\Map\Mapcontroller;
use App\Entity\Map\Mapdetails;
use App\Entity\Map\Mappublicationtext;
use App\Entity\Map\Mapspecialmapsheet;
use App\Form\backend\Map\MapCommentType;
use App\Form\backend\Map\MapControllerType;
use App\Form\backend\Map\MapPublicationtextType;
use App\Form\backend\Map\MapSpecialmapsheetType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/admin/map/edit_oto')]
class MapOTOController extends BackendController
{
    private function getFormType($type): string
    {
        return match ($type) {
            'controller'=>MapControllerType::class,
            'specialmapsheet'=>MapSpecialmapsheetType::class,
            'comment'=>MapCommentType::class,
            'publicationtext'=>MapPublicationtextType::class,
        default => throw new \InvalidArgumentException("No existe formType para '{$type}'.")
        };
    }

    private function getClass($name): string
    {
        return match ($name) {
        'details'=>Mapdetails::class,
        'specialmapsheet'=>Mapspecialmapsheet::class,
        'controller'=>Mapcontroller::class,
        'comment'=>Mapcomment::class,
        'publicationtext'=>Mappublicationtext::class,
        default => throw new \InvalidArgumentException("No existe Entity para '{$name}'.")
        };
    }

    #[Route(path: '/{relationship}/{id}', name: 'admin_map_oto_edit')]
    public function editRelationshipAction(Request $request, Map $map, string $relationship, EntityManagerInterface $em, UrlGeneratorInterface $urlGenerator): Response
    {
        try {
            $type= $this->getFormType($relationship);
            $class= $this->getClass($relationship);
        }catch (\Exception $exception){
            throw new BadRequestHttpException($exception->getMessage());
        }

        $res= $em->getRepository($class)->findBy(['map'=>$map]);
        $entity= (!count($res))? new $class($map) : $res[0];
        $form= $this->createForm($type, $entity)->handleRequest($request);
        $twigArgs=[
            'entity' => $map,
            'relationship'=>$relationship,
            'relEntity'=>$entity,
            'relType'=>'oto',
            'form'=>$form->createView()
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
            //El formulario es una relación one to one de Map. actualizamos la cache de la entidad
//            $factory = new MapSerializerFactory($urlGenerator);
//            $factory->createPartial('');
//            $this->updateCache($entity, $urlGenerator);
            return new JsonResponse(null , 200);

        }catch (\Exception $e){
            return $this->getJsonExceptionErrorResponse($e);
        }
    }
    
    

    #[Route(path: '/delete/{relationship}/{id}', name: 'admin_map_oto_delete',
        requirements: ['id' => '\w+', 'relationship' => '(\w+)'])]
    public function deleteRelationshipAction(Request $request, Map $entity, string $relationship,  EntityManagerInterface $em, TranslatorInterface $translator): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete_oto_'.$relationship.'_'.$entity->getId(), $request->get('_token'))) {
            try{
                $class= $this->getClass($relationship);
                $repo= $em->getRepository($class);
                $res= $repo->findBy(['map'=>$entity]);

                if(!count($res)) {
                    $this->addFlash('danger', 'No data found for relationship ' . $relationship);
                }else{
                    $em->remove($res[0]);
                    $em->flush();
                    $this->addFlash('success', $translator->trans('msg.delete.success', [], 'cavemessages') );
                }
            }catch(\Exception $ex){
                $this->addFlash('danger', $ex->getMessage() );
            }
        }else{
            $this->addFlash('danger', $translator->trans('form.invalidtoken', [], 'validators'));
        }
        return $this->redirectToRoute('admin_map_oto_edit', ['id'=>$entity->getId(), 'relationship'=>$relationship]);
    }

}