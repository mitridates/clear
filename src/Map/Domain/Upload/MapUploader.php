<?php
namespace App\Map\Domain\Upload;
use App\Map\Domain\Entity\Map\Mapimage;
use App\Shared\Upload\TmpFileCache;
use App\Shared\Upload\UploaderParameters;
use App\Shared\Upload\UploadImageTrait;
use App\Shared\Upload\UploadTrait;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class MapUploader
{
    use UploadImageTrait, UploadTrait;

    private UploaderParameters $p;
    public TmpFileCache $fileCache;

    /**
     * @throws \Exception
     */
    public function __construct(UploaderParameters $uploadParameters)
    {
        $this->p= $uploadParameters;
        $this->fileCache= new TmpFileCache($this->p->getCache());
        $this->check();
    }

    /**
     * @throws \Exception
     */
    private function check(): void
    {
        if(!is_dir($this->p->getAbsUploadDir())){
            throw new \Exception('Upload directory "%s" does not exist', $this->p->getAbsUploadDir());
        }
        if(!is_dir($this->p->getAbsUploadThumbDir()))
        {
            throw new \Exception('Upload thumb directory "%s" does not exist', $this->p->getAbsUploadThumbDir());
        }
    }

    /**
     * @param Mapimage $mapImage
     * @param UploadedFile $uploadedFile
     * @return File
     */
    public function uploadFile(Mapimage &$mapImage, UploadedFile $uploadedFile): \SplFileInfo
    {
        $p=& $this->p;
        //caché old file name
        if($mapImage->getFilename())
        {
            $this->fileCache->add($p->getFilePath($mapImage->getFilename()),);
        }

        $generatedFileName = $this->generateFileName($uploadedFile);
        while (file_exists($p->getAbsUploadDir() . DIRECTORY_SEPARATOR . $generatedFileName)) {
            $generatedFileName = $this->generateFileName($uploadedFile);
        }

        $file=  $uploadedFile->move($p->getAbsUploadDir(), $generatedFileName);

        //file is uploaded, update entity data
        $mapImage->setFilename($generatedFileName)
            ->setMimetype($uploadedFile->getMimeType())
            ->setFilesize($uploadedFile->getSize());

        return $file;
    }

    public function uploadThumb(Mapimage &$mapImage, UploadedFile $uploadedFile, \SplFileInfo $file= null): File
    {
        if(!$file)
        {
            $file= new \SplFileInfo($this->p->getFilePath($mapImage->getFilename()));
        }

        $thumbName= $this->generateThumbnailFileName($file, $uploadedFile->getExtension());
        $file= $uploadedFile->move($this->p->getAbsUploadThumbDir(), $thumbName);
        $mapImage->setThumbfilename($thumbName);
        return $file;
    }


}