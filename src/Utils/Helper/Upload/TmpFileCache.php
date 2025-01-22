<?php

namespace App\Utils\Helper\Upload;
use PHPUnit\Util\Exception;

class TmpFileCache
{

    private array $files=[];

    /**
     * @param array{
     *      dir: string,
     *      absDir: string,
     *  } $conf
     */
    public function __construct(private readonly array $conf)
    {

    }

    public function getCacheDir(): string
    {
        return $this->conf['absDir'];
    }
    public function add(string $file): void
    {


        if(!is_file($file)){
            throw new Exception('File not found at: '.$file);
        }
        $tmp= $this->conf['absDir'].DIRECTORY_SEPARATOR.rand();

        $this->files[]=[
            'file'=>$file,
            'tmp'=>$tmp,

        ];
        rename($file, $tmp);
    }

    public function removeAll(): void
    {
        $files = glob($this->conf['absDir'].'/*');

        if(!$files) return;

        foreach($files as $file) {
            if(is_file($file)) unlink($file);
        }
    }

    public function clearCache(): void
    {
        foreach($this->files as $file) {
            $f= $file['absCacheFilePath'];
            if(is_file($f)) unlink($file);
        }
        $this->files=[];
    }

    public function restoreCache(): void
    {
        foreach($this->files as $file) {

            $f= $file['tmp'];
            if(is_file($f)) rename($file['tmp'], $file['file']);
        }

        $this->files=[];
    }

}

