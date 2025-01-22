<?php
namespace  App\Entity\Map\Trait;
use App\Entity\Map\Map;
use App\Entity\Map\Mapcomment;
use App\Entity\Map\Mapcontroller;
use App\Entity\Map\Mapdetails;
use App\Entity\Map\Mappublicationtext;
use App\Entity\Map\Mapspecialmapsheet;
use Doctrine\ORM\Mapping as ORM;

trait MapOneToOneRelationshipTrait
{
    /**
     * PL0203 Map details.
     */
    #[ORM\OneToOne(mappedBy: 'map', targetEntity: Mapdetails::class, cascade: ['persist', 'remove'])]
    private ?Mapdetails $mapdetails;
    /**
     * L0558 Special published sheet name
     */
    #[ORM\OneToOne(mappedBy: 'map', targetEntity: Mapspecialmapsheet::class, cascade: ['persist', 'remove'])]
    private ?Mapspecialmapsheet $mapspecialmapsheet;

    /**
     * Current map controller, PL0406.
     */
    #[ORM\OneToOne(mappedBy: 'map', targetEntity: Mapcontroller::class, cascade: ['persist', 'remove'])]
    private ?Mapcontroller $mapcontroller;

    /**
     * PL0579 Map comment.
     */
    #[ORM\OneToOne(mappedBy: 'map', targetEntity: Mapcomment::class, cascade: ['persist', 'remove'])]
    private ?Mapcomment $mapcomment;

    /**
     * PL0219 Map publication no id
     */
    #[ORM\OneToOne(mappedBy: 'map', targetEntity: Mappublicationtext::class, cascade: ['persist', 'remove'])]
    private ?Mappublicationtext $mappublicationtext;

    public function getMapdetails(): ?Mapdetails
    {
        return $this->mapdetails;
    }

    public function getMapspecialmapsheet(): ?Mapspecialmapsheet
    {
        return $this->mapspecialmapsheet;
    }

    public function getMapcontroller(): ?Mapcontroller
    {
        return $this->mapcontroller;
    }

    public function getMapcomment(): ?Mapcomment
    {
        return $this->mapcomment;
    }

    public function getMappublicationtext(): ?Mappublicationtext
    {
        return $this->mappublicationtext;
    }
}