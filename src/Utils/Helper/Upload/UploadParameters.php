<?php
namespace App\Utils\Helper\Upload;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class UploadParameters
{
    public array $p;
    /**
     * @throws \Exception
     */
    public function __construct(ParameterBagInterface $bag, string $resource)
    {
        $config = $bag->get('cave')['upload'];
        $res=& $config['resource'][$resource];
        $kpd= $bag->get('kernel.project_dir');
        $p= [
            'kernelProjectDir' => $kpd,
            'absUploadDir'=>null,
            'cache'=>[
                'dir'=>'/tmp',
                'absDir'=>null,
            ],
            'fileNameProperties' => [
                'maxLength' => 50,
                'uidLength' => 5,
                'prefix'=>'defaultPrefix_'
            ],
            # GD auto generated thumbnail properties
            'thumbnailProperties' => [
                'thumbPrefix' => "thumb_",
# Anchor (Crop image from this point):
# 'center', 'top', 'bottom', 'left', 'right', 'top left', 'top right', 'bottom left', 'bottom right'
# (default 'center').
                'anchor' => 'center',
                'width' => 255,
                'height' => 170,
                'minWidth' => 180,
                'minHeight' => 120,
                'mime' => 'image/jpeg',
                'quality' => 75
            ],
            'resource'=>[
                'prefix'=>null,
                'uploadDir'=>null,
                'thumbDir'=>null//'/thumbnail',
            ]
        ];

        if(isset($config['options']))
        {
            foreach (['cache', 'fileNameProperties', 'thumbnailProperties'] as $o) {
                if(isset($config[$o]))
                {
                    $p[$o] = array_merge_recursive($p[$o], $config[$o]);
                }
            }
        }

        $res= array_merge($p['resource'], $res);

        $p['absUploadDir']= self::getAbsResourceDir($kpd, $res['uploadDir']);
        if($res['thumbDir'])
        {
            $p['absUploadThumbDir']= self::getAbsResourceDir(
                $kpd,
                $res['uploadDir'].$res['thumbDir']
            );
        }

        $p['resource']= $res;

        if(isset($res['prefix'])){
            $p['fileNameProperties']['prefix']= $res['prefix'];
        }

        if(!$p['cache']['absDir']){
            $p['cache']['absDir']= $p['absUploadDir'].$p['cache']['dir'];
        }
        $this->p= $p;
    }


    /**
     * @throws \Exception
     */
    private static function getAbsResourceDir($kernelProjectDir, $configDir): string
    {
        if(!is_dir($kernelProjectDir)){
            throw new \Exception(sprintf("Invalid kernel project directory '%s'", $kernelProjectDir));
        }
        $ret= $kernelProjectDir.$configDir;
        if(!file_exists($ret)){
            throw new \Exception(sprintf('Absolute directory path "%s" not found', $ret));
        }
        return $ret;
    }

    /**
     * @param string $fileName
     * @return string Path to file
     */
    public function getFilePath(string $fileName): string
    {
        return $this->p['absUploadDir'].DIRECTORY_SEPARATOR.$fileName;
    }

    /**
     * @return array{
     *     kernelProjectDir: string,
     *     absUploadDir: string,
     *     cache:array,
     *     fileNameProperties: array,
     *     thumbnailProperties:array,
     *     resource: array
     * }
     */
    public function getParameters(): array
    {
       return $this->p;
    }

    public function getAbsUploadDir(): String
    {
        return $this->p['absUploadDir'];
    }
    public function getAbsUploadThumbDir(): String
    {
        return $this->p['absUploadThumbDir'];
    }

    //@todo y si la extensión del thumb es distinta de la imagen?
    public function getThumbFilePath($fileName): string
    {
        return $this->getAbsUploadThumbDir().DIRECTORY_SEPARATOR.$fileName;
    }

    /**
     * @return array{
     *     thumbPrefix:string,
     *     anchor: string,
     *     width: int,
     *     height: int,
     *     minWidth: int,
     *     minHeight: int,
     *     mime: string,
     *     quality: int
     *  }
     */
    public function getThumbnailProperties(): array
    {
        return $this->p['thumbnailProperties'];
    }

    /**
     * @return array{
     *     maxLength: int,
     *     uidLength: int,
     *     prefix: string,
     * }
     */
    public function getFileNameProperties(): array
    {
        return $this->p['fileNameProperties'];
    }

    /**
     * @return array{
     *     dir: string,
     *     absDir: string,
     * }
     */
    public function getCache(): array
    {
        return $this->p['cache'];
    }

}