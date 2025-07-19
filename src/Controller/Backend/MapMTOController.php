<?php
namespace App\Controller\Backend;
use App\Controller\BackendController;
use App\Entity\Map\Map;
use App\Entity\Map\Mapimage;
use App\Manager\MapManager;
use App\Utils\Helper\MapControllerHelper;
use App\Utils\Helper\Upload\MapUploaderHelper;
use App\Utils\Json\JsonErrorSerializer\JsonErrorBag;
use App\vendor\tobscure\jsonapi\Collection;
use App\vendor\tobscure\jsonapi\Document;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/admin/map/edit_mto')]
class MapMTOController extends BackendController
{

    #[Route(path: '/{relationship}/{id}', name: 'admin_map_mto_index')]
    public function indexAction(Request $request, Map $entity, string $relationship): Response
    {
        $type= MapControllerHelper::MTO_FORM_TYPE[$relationship];
        $class= MapControllerHelper::MTO_RELATIONSHIP[$relationship];
        $form = $this->createForm($type, new $class($entity));
        $twigArgs=[
            'relationship'=>$relationship,
            'relType'=>'mto',
            'form'   => $form->createView(),
            'entity'     => $entity

        ];
        return $this->render('@admin/map/edit.html.twig',$twigArgs);
    }

    #[Route(path: '/list/{relationship}/{id}', name: 'admin_map_mto_list',
        requirements: ['id'=>'\w+', 'relationship' => '(\w+)'])]
    public function listJsonAction(Request $request, Map $entity, string $relationship, MapManager $manager, ParameterBagInterface $bag,  UrlGeneratorInterface $urlGenerator, CsrfTokenManagerInterface $tokenManager): Response
    {
        $class= MapControllerHelper::MTO_RELATIONSHIP[$relationship];
        $serializer= MapControllerHelper::MTO_SERIALIZER[$relationship];
        $serializerFields= MapControllerHelper::MTO_SERIALIZER_FIELDS[$relationship];

        $this->acceptOnlyXmlHttpRequest($request);
        $listOptions= $this->getRequestListOptions($request);
        list($paginator, $data) = $manager->paginateRelationship(new $class($entity), $listOptions);


        $collection= new Collection($data, new $serializer($urlGenerator ,  $tokenManager));
        if(isset($serializerFields['fields'])){
            $collection->fields($serializerFields['fields']);
        }
        if(isset($serializerFields['with'])){
            $collection->with($serializerFields['with']);
        }


        $document = (new Document($collection));
        $document->addMeta('pagination', $paginator->toArray());
        return new JsonResponse($document , 200, ['Content-Type'=>$document::MEDIA_TYPE]);

    }

    #[Route(path: '/json/new/{id}/{relationship}', name: 'admin_map_mto_new',
        requirements: ['id'=>'\w+', 'relationship' => '(\w+)'])]

    public function newRelationshipAction(Request $request, Map $entity, string $relationship, EntityManagerInterface $em, ParameterBagInterface $bag,): Response
    {
        $class= MapControllerHelper::MTO_RELATIONSHIP[$relationship];
        $type= MapControllerHelper::MTO_FORM_TYPE[$relationship];
        $form = $this->createForm($type, new $class($entity))->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $rel = $form->getData();

            if($relationship==='image')
            {
                $this->upload($form, $rel, $bag);
            }

            try {
                $em->persist($rel);
                $em->flush();
                $em->clear();
                return new JsonResponse(null , 200, ['Content-Type'=>Document::MEDIA_TYPE]);
            }catch (\Exception $e){
                return $this->getJsonExceptionErrorResponse($e);
            }
        }else{
            return $this->getJsonFormErrorResponse($form);
        }
    }



    #[Route(path: '/update/{relationship}/{id}/{sequence}/{req}', name: 'admin_map_mto_update',
        requirements: ['id' => '\w+', 'relationship' => '(\w+)', 'sequence' => '\d+', 'req' => 'get|set'])]
    public function updateRelationshipAction(Request $request, Map $entity, string $relationship, string $sequence, string $req, EntityManagerInterface $em, ParameterBagInterface $bag, FormFactoryInterface $formFactory): Response
    {
        $class= MapControllerHelper::MTO_RELATIONSHIP[$relationship];
        $type= MapControllerHelper::MTO_FORM_TYPE[$relationship];
        $repo= $em->getRepository($class);
        $data= $repo->findOneBy(['sequence'=>$sequence, 'map'=>$entity]);
        $nmsArr= explode("\\", $class);
        $formName= 'modal'.strtolower(end($nmsArr));
        if($req==='get')
        {
            $twigArgs=[
                'relationship'=>$relationship,
                'sequence'=>$sequence,
                'entity'=>$entity,
                'form'=>$formFactory->createNamedBuilder($formName, $type, $data)->getForm()
            ];
            return $this->render('@admin/map/_form_mto_modal.html.twig',$twigArgs);
        }
        $form = $formFactory->createNamedBuilder($formName, $type, new $class($entity))->getForm()->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $rel = $form->getData();
            if($relationship==='image')
            {
                try {
                    $this->upload($form, $rel, $bag);
                }catch (\Exception $e){
                    return $this->getJsonExceptionErrorResponse($e);
                }
            }
            try {
                $em->persist($rel);
                $em->flush();
                $em->clear();
            }catch (\Exception $e){
                return $this->getJsonExceptionErrorResponse($e);
            }
        }else{
            return $this->getJsonFormErrorResponse($form);
        }
        return new JsonResponse(null , 200, ['Content-Type'=>Document::MEDIA_TYPE]);
    }

    #[Route(path: '/map/delete/{id}/{relationship}/{sequence}', name: 'admin_map_mto_delete',
        requirements: ['id' => '\w+', 'relationship' => '(\w+)', 'sequence' => '\d+'])]
    public function deleteRelationshipAction(Request $request, Map $entity, string $relationship, string $sequence, EntityManagerInterface $em, TranslatorInterface $translator): JsonResponse
    {
        $class= MapControllerHelper::MTO_RELATIONSHIP[$relationship];
        $repo= $em->getRepository($class);
        $repo->findOneBy(['sequence'=>$sequence, 'map'=>$entity]);
        $tokenId=$relationship.$entity->getId().$sequence.'_delete_token';

        return call_user_func_array([$this, 'CommonBackendXmlHttpRequestDeleteAction'],
            array_merge(func_get_args(), [
                    'entity'=>$repo->findOneBy(['sequence'=>$sequence, 'map'=>$entity]),
                    'tokenId'=>$tokenId
            ]));
    }


    /**
     * @throws \Exception
     */
    private function upload(FormInterface $form, Mapimage &$mapImage, ParameterBagInterface $bag): \Exception|Mapimage
    {
        $mapUploaderHelper= new MapUploaderHelper($bag);
        /** @var ?UploadedFile $uploadedFile */
        $uploadedFile = $form->get('mapfile')->getData();
        /** @var UploadedFile|null $thumbUploadedFile */
        $thumbUploadedFile = $form->get('thumbnail')->getData();
        $updateThumb = $form->has('updatethumb')? $form->get('updatethumb') : null;
        $is_new= $mapImage->getFilename()===null;

        if(!$uploadedFile && $is_new)
        {
            return new \Exception('New registry MUST CONTAIN map file');
        }

        if($uploadedFile)
        {
            $is_image= $mapUploaderHelper::isImage($uploadedFile);
            try {
                $f= $mapUploaderHelper->uploadFile($mapImage, $uploadedFile);
            }catch (\Exception $e){
                return $e;
            }

            if(!$thumbUploadedFile && $is_image && !$mapImage->getThumbfilename())
            {
                $mapUploaderHelper->createThumbnailFromImageFile($f);
            }

        }

        if($thumbUploadedFile)//types validator in formType
        {
            if($is_new){
                return new \Exception('THUMB NOT ALLOWED  if main File does not exists');
            }
            $mapUploaderHelper->uploadThumb($mapImage, $thumbUploadedFile);
        }

        $mapUploaderHelper->fileCache->clearCache();

        return $mapImage;
    }
}