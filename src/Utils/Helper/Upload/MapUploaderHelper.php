<?php
namespace App\Utils\Helper\Upload;
use App\Entity\Map\Mapimage;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class MapUploaderHelper
{
    use UploadImageTrait, UploadTrait;

    private UploadParameters $p;
    public TmpFileCache $fileCache;

    /**
     * @throws \Exception
     */
    public function __construct(ParameterBagInterface $bag, protected ?SluggerInterface $slugger= null)
    {
        $this->p= new UploadParameters($bag, 'map');
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