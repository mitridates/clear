<?php

namespace App\Shared\Upload;

use App\Utils\SimpleImage;

trait UploadImageTrait
{
    private UploaderParameters $p;

    public static function isImage(\SplFileInfo|string $file):false|array
    {
        if($file instanceof \SplFileInfo){
            $file= $file->getPathname();
        }
        $arr= getimagesize($file);
        return @is_array($arr)? $arr:false;
    }


    /**
     * @param \SplFileInfo $from
     * @return \SplFileInfo
     * @throws \Exception
     */
    public function createThumbnailFromImageFile(\SplFileInfo $from): \SplFileInfo
    {
        $props= $this->p->getThumbnailProperties();
        $simpleImage = new SimpleImage($from->getPath());
        $simpleImage->thumbnail($props['width'], $props['height']);


        $ext= $this->get_image_extension($props['mime']);
        if(!$ext){
            $ext= image_type_to_extension(getimagesize($from->getRealPath())[2], true);
        }

        $this->generateThumbnailFileName($from, $ext);

        $to= $this->p->getThumbFilePath( $this->generateThumbnailFileName($from, $ext));

        $simpleImage->toFile($to,
            $props['mime'],
            $props['quality']);
        return new \SplFileInfo($to);
    }

    /**
     * @param string $rawName
     * @param string $extension
     * @return string
     */
    private function generateThumbnailFileName(string|\SplFileInfo $rawName, string $extension): string
    {
        if($rawName instanceof \SplFileInfo){
            $rawName= $rawName->getBasename($rawName->getExtension());
        }

        $props= $this->p->getThumbnailProperties();

        return $props['prefix'] .DIRECTORY_SEPARATOR. $rawName.'.'.$extension;
    }

    function get_image_extension(string $mimetype): bool|string
    {
        return match ($mimetype) {
            'image/bmp' => '.bmp',
            'image/cis-cod' => '.cod',
            'image/gif' => '.gif',
            'image/ief' => '.ief',
            'image/jpeg' => '.jpg',
            'image/pipeg' => '.jfif',
            'image/tiff' => '.tif',
            'image/x-cmu-raster' => '.ras',
            'image/x-cmx' => '.cmx',
            'image/x-icon' => '.ico',
            'image/x-portable-anymap' => '.pnm',
            'image/x-portable-bitmap' => '.pbm',
            'image/x-portable-graymap' => '.pgm',
            'image/x-portable-pixmap' => '.ppm',
            'image/x-rgb' => '.rgb',
            'image/x-xbitmap' => '.xbm',
            'image/x-xpixmap' => '.xpm',
            'image/x-xwindowdump' => '.xwd',
            'image/png' => '.png',
            'image/x-jps' => '.jps',
            'image/x-freehand' => '.fh',
            default => false,
        };
    }
}