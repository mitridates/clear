<?php

use Doctrine\ORM\Mapping as ORM;

trait FileTrait
{

    /**
     *  Map file name. FD 590
     */
    #[ORM\Column(name: 'filename', type: 'string', length: 50, nullable: true)]
    private ?string $filename= null;

//    /**
//     *  Path under /public/.... FD 596
//     */
//    #[ORM\Column(name: 'directorypath', type: 'string', length: 50, nullable: true)]
//    private ?string $directorypath;

    /**
     *  uncoded
     */
    #[ORM\Column(name: 'mimetype', type: 'string', length: 50, nullable: true)]
    private ?string $mimetype;

    /**
     *  uncoded
     */
    #[ORM\Column(name: 'filesize', type: 'string', length: 10, nullable: true)]
    private ?string $filesize;

    /**
     *  FD 10108
     */
    #[ORM\Column(name: 'name', type: 'string', length: 70, nullable: true)]
    private ?string $name;

    /**
     *  FD 597
     */
    #[ORM\Column(name: 'comment', type: 'string', length: 70, nullable: true)]
    private ?string $comment;


    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): self
    {
        $this->filename = $filename;

        return $this;
    }
//
//    public function getDirectorypath(): ?string
//    {
//        return $this->directorypath;
//    }
//
//    public function setDirectorypath(?string $directorypath): self
//    {
//        $this->directorypath = $directorypath;
//
//        return $this;
//    }

    public function getMimetype(): ?string
    {
        return $this->mimetype;
    }

    public function setMimetype(?string $mimetype): self
    {
        $this->mimetype = $mimetype;

        return $this;
    }

    public function getFilesize(): ?string
    {
        return $this->filesize;
    }

    public function setFilesize(?string $filesize): self
    {
        $this->filesize = $filesize;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }
    
    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }
}

