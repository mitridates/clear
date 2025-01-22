<?php

namespace App\Utils\Helper\Upload;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\String\Slugger\SluggerInterface;

trait UploadTrait
{
    private UploadParameters $p;
    protected ?SluggerInterface $slugger;
    /**
     * @param File $file
     * @return string
     */
    protected function generateFileName(File $file): string
    {
        if(!$this->slugger) $this->slugger = new AsciiSlugger();
        $props= $this->p->getFileNameProperties();
        $uidLength= (int)$props['uidLength'];
        $prefix= (string)$props['prefix'];
        $maxLength= $props['maxLength']??50;

        $safeFilename = $this->slugger->slug($file->getBasename());
        $ext = '.' . $file->guessExtension();
        $uid = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $uidLength);
        $filename = $prefix . $uid . '-' . $safeFilename;

        while (strlen($filename . $ext) > $maxLength) {
            $filename = substr($filename, 0, -1);
        }
        return $filename.'.'.$file->guessExtension();
    }

}