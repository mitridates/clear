<?php
namespace App\Utils\Helper\Upload;
use App\Mitridates\Cavern\Entity\Mapimage;
use App\Utils\Arraypath;
use App\Utils\Upload\SimpleImage;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploaderHelper
{
    private FileUploader $uploader;
    private Arraypath $data;
    private Arraypath $resource;
    private Arraypath $parameters;

    public function __construct(Arraypath $parameters, Arraypath $resource)
    {
        $this->parameters= $parameters;
        $this->resource= $resource;
        $this->data= new Arraypath();
        $this->uploader= new FileUploader();
    }


    /**
     * @throws \Exception
     */
    public function uploadFile(UploadedFile $file, string $prefix , bool $try_gd=false): Arraypath
    {
        $this->data
            ->set('mime', $file->getMimeType())
            ->set('size', $file->getSize())
            ->set('is_image', str_contains($file->getMimeType(), 'image') && getimagesize($file->getPathname()))
            ;

            $fileName= $this->uploader->upload(
                $file,
                $this->getAbsPath(),
                $this->parameters->get('fileName:maxLength', 50),
                $this->resource->get('prefix', '').$prefix,
                $this->parameters->get('fileName:uidLength', 5),
            );

        $this->data->set('fileName', $fileName);



        if($try_gd && $this->data->get('is_image'))
        {
            $this->createThumbnailFromGd($fileName);
        }

        return $this->data;

    }

    /**
     * @throws \Exception
     */
    public function uploadThumbFile(UploadedFile $file, $origenFileName)
    {
        if(!getimagesize($file->getRealPath()))
        {
            throw new \Exception(sprintf('Invalid Thumb file type "%s", expected "%s"', $file->getMimeType(), 'image/*'));
        }

        $thumbFileName= $this->createThumbFileName($origenFileName);//generar nombre

        $file->move($this->getThumbAbsPath(), $thumbFileName);
        $this->data->set('thumbFileName', $thumbFileName);

        return $this->data;
    }

    /**
     * @throws \Exception
     */
    private function createThumbnailFromGd(string $parentFileName): bool|Arraypath
    {
        if(!extension_loaded('gd')) return false;

        $fileRealPath= $this->getRealPath($parentFileName);

        if(!file_exists($fileRealPath)){
            throw new \Exception(sprintf('File %s not found', $fileRealPath));
        };

        $props= $this->parameters->get('thumbnailProperties');
        $thumbName= $this->createThumbFileName($parentFileName);
        $thumbRealPath= $this->getThumbRealPath($thumbName);

        $simpleImage= new SimpleImage($fileRealPath);
        $simpleImage->thumbnail($props['width'], $props['height']);
        $simpleImage->toFile($thumbRealPath, $props['mime'], $props['quality']);

        return $this->data->set('thumbFileName', $thumbName);

    }
//
//    public function updateUpload(UploadedFile $uploadedFile, $prefix=null)
//    {
//
//        if($thumbUploadedFile)
//        {
//            $ret= $this->uploadThumbFile($thumbUploadedFile, $fileName??$this->entity->getFilename());
//            if($ret instanceof \Exception) return $this->util->error()->addException($ret)->getJsonResponse();
//
//        }elseif(
//            $uploadedFile
//            && isset($fileName)
//            && !$this->entity->getThumbfilename()
//            && extension_loaded('gd')
//            && getimagesize($this->absPath.$fileName)
//        ) {
//            $ret = $this->createThumbnailFromGd($fileName);
//            if ($ret instanceof \Exception) {
//                return $this->util->error()->addException($ret)->getJsonResponse();
//            }
//        }
//        return $this->entity;
//    }

/////
////////////
////////////////

//    public function updateUpload(): Mapimage|JsonResponse
//    {
//        /** @var UploadedFile $uploadedFile */
//        $uploadedFile = $this->form->get('mapfile')->getData();
//        /** @var UploadedFile $thumbUploadedFile */
//        $thumbUploadedFile = $this->form->get('thumbnail')->getData();
//
//        if ($uploadedFile)
//        {
//            $fileName= $this->uploadFile($uploadedFile);
//            if($fileName instanceof \Exception) return $this->util->error()->addException($fileName)->getJsonResponse();
//        }
//
//        if($thumbUploadedFile)
//        {
//            $ret= $this->uploadThumbFile($thumbUploadedFile, $fileName??$this->entity->getFilename());
//            if($ret instanceof \Exception) return $this->util->error()->addException($ret)->getJsonResponse();
//
//        }elseif(
//            $uploadedFile
//            && isset($fileName)
//            && !$this->entity->getThumbfilename()
//            && extension_loaded('gd')
//            && getimagesize($this->absPath.$fileName)
//        ) {
//            $ret = $this->createThumbnailFromGd($fileName);
//            if ($ret instanceof \Exception) {
//                return $this->util->error()->addException($ret)->getJsonResponse();
//            }
//        }
//        return $this->entity;
//    }
//
//    public function newUpload(UploadedFile $uploadedFile): Mapimage|JsonResponse
//    {
//        $fileName= $this->uploadFile($uploadedFile);
//        /** @var UploadedFile $uploadedFile */
//        $uploadedFile = $this->form->get('mapfile')->getData();
//        /** @var UploadedFile $thumbUploadedFile */
//        $thumbUploadedFile = $this->form->get('thumbnail')->getData();
//
//        $fileName= $this->uploadFile($uploadedFile);
//        if($fileName instanceof \Exception) return $this->util->error()->addException($fileName)->getJsonResponse();
//
//        if($thumbUploadedFile)
//        {
//            $ret= $this->uploadThumbFile($thumbUploadedFile, $fileName);
//            if($ret instanceof \Exception) return $this->util->error()->addException($ret)->getJsonResponse();
//
//        }elseif(
//            extension_loaded('gd')
//            && getimagesize($this->absPath.$fileName)
//        ){
//            $ret = $this->createThumbnailFromGd($fileName);
//            if ($ret instanceof \Exception) {
//                return $this->util->error()->addException($ret)->getJsonResponse();
//            }
//        }
//
//        return $this->entity;
//    }


    public function getResource(): Arraypath
    {
        return $this->resource;
    }

    public function deleteFile($fileName):UploaderHelper
    {
        try { unlink($this->getRealPath($fileName)); }catch (\Exception $e){}
        return $this;
    }
    public function deleteThumb($fileName):UploaderHelper
    {
        try { unlink($this->getThumbRealPath($fileName)); }catch (\Exception $e){}
        return $this;
    }

    private function getAbsPath(): string
    {
        return $this->parameters->get('projectDir').$this->resource->get('dir');
    }
    private function getThumbAbsPath(): string
    {
        return $this->parameters->get('projectDir').$this->resource->get('thumbnailDir');
    }

    private function getRealPath($fileName): string
    {
        return $this->getAbsPath().$fileName;
    }

    public function createThumbFileName($parentFileName): string
    {
        return  $this->resource->get('thumbPrefix', 'thumb_').$parentFileName;
    }

    private function getThumbRealPath($fileName): string
    {
        return $this->getThumbAbsPath().$fileName;
    }


}