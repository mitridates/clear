<?php
namespace App\Map\Infrastructure\Controller;
use App\Map\Domain\Entity\Map\Map;
use App\Map\Domain\Entity\Map\Mapcave;
use App\Map\Domain\Entity\Map\Mapcitation;
use App\Map\Domain\Entity\Map\Mapcomment;
use App\Map\Domain\Entity\Map\Mapdrafter;
use App\Map\Domain\Entity\Map\Mapfurthergc;
use App\Map\Domain\Entity\Map\Mapfurtherpc;
use App\Map\Domain\Entity\Map\Mapimage;
use App\Map\Domain\Entity\Map\Maplink;
use App\Map\Domain\Entity\Map\Mapsurveyor;
use App\Map\Domain\Manager\MapManager;
use App\Map\Domain\Serialization\MapSerializerRegistry;
use App\Map\Domain\Upload\MapUploader;
use App\Map\UI\Form\MapCaveType;
use App\Map\UI\Form\MapCitationType;
use App\Map\UI\Form\MapCommentType;
use App\Map\UI\Form\MapDrafterTypeToOne;
use App\Map\UI\Form\MapFurthergcType;
use App\Map\UI\Form\MapFurtherpcType;
use App\Map\UI\Form\MapImageType;
use App\Map\UI\Form\MapLinkType;
use App\Map\UI\Form\MapSurveyorType;
use App\Shared\Infrastructure\Controller\BackendController;
use App\Shared\tobscure\jsonapi\Collection;
use App\Shared\tobscure\jsonapi\Document;
use App\Shared\Upload\UploaderParameters;
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
    private function getFormType($type): string
    {
        return match ($type) {
            'cave'=> MapCaveType::class,
            'citation'=>MapCitationType::class,
            'comment'=>MapCommentType::class,
            'drafter'=>MapDrafterTypeToOne::class,
            'furthergc'=>MapFurthergcType::class,
            'furtherpc'=>MapfurtherpcType::class,
            'image'=>MapImageType::class,
            'link'=>MapLinkType::class,
            'surveyor'=>MapSurveyorType::class,
            default => throw new \InvalidArgumentException("No existe formType para '{$type}'.")
        };
    }


    private function getClass($name): string
    {
        return match ($name) {
            'cave'=> Mapcave::class,
            'citation'=>Mapcitation::class,
            'comment'=>Mapcomment::class,
            'drafter'=>Mapdrafter::class,
            'furthergc'=>Mapfurthergc::class,
            'furtherpc'=>Mapfurtherpc::class,
            'image'=>Mapimage::class,
            'link'=>Maplink::class,
            'surveyor'=>Mapsurveyor::class,
            default => throw new \InvalidArgumentException("No existe Entity para '{$name}'.")
        };
    }
    #[Route(path: '/{relationship}/{id}', name: 'admin_map_mto_index')]
    public function indexAction(Request $request, Map $entity, string $relationship): Response
    {
        $type= $this->getFormType($relationship);
        $class= $this->getClass($relationship);
        $form = $this->createForm($type, new $class($entity));
        $twigArgs=[
            'relationship'=>$relationship,
            'relType'=>'mto',
            'form'   => $form->createView(),
            'entity'     => $entity

        ];
        return $this->render('@admin/map/edit.html.twig',$twigArgs);
    }

    /**
     * @throws \Exception
     */
    #[Route(path: '/list/{relationship}/{id}', name: 'admin_map_mto_list',
        requirements: ['id'=>'\w+', 'relationship' => '(\w+)'])]
    public function listJsonAction(Request $request, Map $entity, string $relationship, MapManager $manager, ParameterBagInterface $bag,  UrlGeneratorInterface $urlGenerator, CsrfTokenManagerInterface $tokenManager): Response
    {
        $class= $this->getClass($relationship);
        $serializer= MapSerializerRegistry::MTO_SERIALIZER[$relationship];
        $serializerFields= MapSerializerRegistry::MTO_SERIALIZER_FIELDS[$relationship];

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
        $class= $this->getClass($relationship);
        $form = $this->createForm($this->getFormType($relationship),
            new $class($entity))->handleRequest($request);
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
        $class= $this->getClass($relationship);
        $type= $this->getFormType($relationship);
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
        $class= $this->getClass($relationship);
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

        $mapUploaderHelper= new MapUploader(new UploaderParameters(
            $this->getParameter('kernel.project_dir'),
            $this->getBundleParameters(),
            'map'
        ));

        /** @var ?UploadedFile $uploadedFile */
        $uploadedFile = $form->get('mapfile')->getData();
        /** @var UploadedFile|null $thumbUploadedFile */
        $thumbUploadedFile = $form->get('thumbnail')->getData();
       // $updateThumb = $form->has('updatethumb')? $form->get('updatethumb') : null;
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