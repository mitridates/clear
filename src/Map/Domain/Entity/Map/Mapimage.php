<?php

namespace  App\Map\Domain\Entity\Map;

use App\Fielddefinition\Domain\Entity\Fieldvaluecode;
use App\Map\Domain\Entity\Map\Model\MapManyToOneInterface;
use App\Map\Domain\Entity\Map\Trait\MapManyToOneTrait;
use App\Shared\Doctrine\Trait\CrupdatetimeTrait;
use App\Shared\Doctrine\Trait\FileTrait;
use App\Shared\Doctrine\Trait\SequenceTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Mapimage (PL?)
 * * referencias:  (título(name), autor, fecha, nombre del sitio web y URL, licencia),
 * elementos básicos de una referencia: el autor, fecha de publicación, título del trabajo y fuente para recuperación.
 */
#[ORM\Table(name: 'map_image')]
#[ORM\Index(columns: ['map'], name: 'map_idx')]
#[ORM\Index(columns: ['digital_technique'], name: 'image_digitaltechnique_idx')]
#[ORM\Index(columns: ['image_tupe'], name: 'image_type_idx')]
#[ORM\Index(columns: ['image_format'], name: 'image_format_idx')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class Mapimage implements MapManyToOneInterface
{
    use SequenceTrait, CrupdatetimeTrait, MapManyToOneTrait, FileTrait;
    /**
     * FD 195
     */
    #[ORM\ManyToOne(targetEntity:  Map::class, inversedBy: 'mapimage')]
    #[ORM\JoinColumn(name: 'map', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private $map;

    /**
     * Referencia al recurso digital. FD 3072
     */
    #[Assert\Length(max: 200)]
    #[ORM\Column(name: 'map_image_reference', type: 'string', length: 200, nullable: true)]
    private ?string $reference = null;

    /**
     * Cita al recurso digital. FD 3071 uncoded
     */
    #[Assert\Length(max: 40)]
    #[ORM\Column(name: 'map_image_citation', type: 'string', length: 40, nullable: true)]
    private ?string $citation = null;

    /**
     * Map file name. Custom FD 5891 uncoded
     */
    #[ORM\Column(name: 'thumb_filename', type: 'string', length: 56, nullable: true)]
    private ?string $thumbfilename = null;
    
    /**
     * Map software used. FD 591
     */
    #[ORM\Column(name: 'software', type: 'string', length: 30, nullable: true)]
    private ?string $software = null;

    /**
     * Map software version. FD 592
     */
    #[ORM\Column(name: 'softversion', type: 'string', length: 10, nullable: true)]
    private ?string $softversion = null;

    /**
     * FD 593
     */
    #[ORM\ManyToOne(targetEntity: Fieldvaluecode::class)]
    #[ORM\JoinColumn(name: 'digital_technique', referencedColumnName: 'id', nullable: true)]
    private ?Fieldvaluecode $digitaltechnique = null;

    /**
     * FD 594
     */
    #[ORM\ManyToOne(targetEntity: Fieldvaluecode::class)]
    #[ORM\JoinColumn(name: 'image_tupe', referencedColumnName: 'id', nullable: true)]
    private ?Fieldvaluecode $type = null;

    /**
     * FD 595
     */
    #[ORM\ManyToOne(targetEntity: Fieldvaluecode::class)]
    #[ORM\JoinColumn(name: 'image_format', referencedColumnName: 'id', nullable: true)]
    private ?Fieldvaluecode $format = null;

    public function getThumbfilename(): ?string
    {
        return $this->thumbfilename;
    }

    public function setThumbfilename(string $thumbfilename): self
    {
        $this->thumbfilename = $thumbfilename;

        return $this;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    public function getCitation(): ?string
    {
        return $this->citation;
    }

    public function setCitation(string $citation): self
    {
        $this->citation = $citation;

        return $this;
    }
    
    public function getSoftware(): ?string
    {
        return $this->software;
    }

    public function setSoftware(?string $software): self
    {
        $this->software = $software;

        return $this;
    }

    public function getSoftversion(): ?string
    {
        return $this->softversion;
    }

    public function setSoftversion(?string $softversion): self
    {
        $this->softversion = $softversion;

        return $this;
    }

    public function getDigitaltechnique(): ?Fieldvaluecode
    {
        return $this->digitaltechnique;
    }

    public function setDigitaltechnique(?Fieldvaluecode $digitaltechnique): self
    {
        $this->digitaltechnique = $digitaltechnique;

        return $this;
    }

    public function getType(): ?Fieldvaluecode
    {
        return $this->type;
    }

    public function setType(?Fieldvaluecode $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getFormat(): ?Fieldvaluecode
    {
        return $this->format;
    }

    public function setFormat(?Fieldvaluecode $format): self
    {
        $this->format = $format;

        return $this;
    }
}
