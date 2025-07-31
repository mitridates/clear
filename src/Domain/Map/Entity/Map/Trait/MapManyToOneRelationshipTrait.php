<?php
namespace  App\Domain\Map\Entity\Map\Trait;
use App\Domain\Map\Entity\Map\{Mapsurveyor};
use App\Domain\Map\Entity\Map\Map;
use App\Domain\Map\Entity\Map\Mapcave;
use App\Domain\Map\Entity\Map\Mapcitation;
use App\Domain\Map\Entity\Map\Mapcommentline;
use App\Domain\Map\Entity\Map\Mapdrafter;
use App\Domain\Map\Entity\Map\Mapfurthergc;
use App\Domain\Map\Entity\Map\Mapfurtherpc;
use App\Domain\Map\Entity\Map\Mapimage;
use App\Domain\Map\Entity\Map\Maplink;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

trait MapManyToOneRelationshipTrait
{
    ### ONETOMANY PROPERTIES
    /**
     * Caves on map, PL0601.
     */
    #[ORM\OneToMany(mappedBy: Map::class, targetEntity: Mapcave::class, cascade: ['persist', 'remove'], indexBy: 'sequence')]
    private Collection $mapcave;

    /**
     * Map publication, PL0598.
     */
    #[ORM\OneToMany(mappedBy: 'map', targetEntity: Mapcitation::class, cascade: ['persist', 'remove'], indexBy: 'sequence')]
    private Collection $mapcitation;

    /**
     * Map Comment, PL0579.
     */
    #[ORM\OneToMany(mappedBy: 'map', targetEntity: Mapcommentline::class, cascade: ['persist', 'remove'], indexBy: 'sequence')]
    private Collection $mapcommentline;

    /**
     * PL0587.
     */
    #[ORM\OneToMany(mappedBy: 'map', targetEntity: Mapdrafter::class, cascade: ['persist', 'remove'], indexBy: 'sequence')]
    private Collection $mapdrafter;

    /**
     * Further political coverage, PL0368.
     */
    #[ORM\OneToMany(mappedBy: 'map', targetEntity: Mapfurtherpc::class, cascade: ['persist', 'remove'], indexBy: 'sequence')]
    private Collection $mapfurtherpc;

    /**
     * Further geographic coverage, PL0397.
     */
    #[ORM\OneToMany(mappedBy: 'map', targetEntity: Mapfurthergc::class, cascade: ['persist', 'remove'], indexBy: 'sequence')]
    private Collection $mapfurthergc;

    /**
     * Images
     */
    #[ORM\OneToMany(mappedBy: 'map', targetEntity: Mapimage::class, cascade: ['persist', 'remove'], indexBy: 'sequence')]
    private Collection $mapimage;

    /**
     * Links
     */
    #[ORM\OneToMany(mappedBy: 'map', targetEntity: Maplink::class, cascade: ['persist', 'remove'], indexBy: 'sequence')]
    private Collection $maplink;

    /**
     * Map surveyor, PL0586.
     */
    #[ORM\OneToMany(mappedBy: 'map', targetEntity: Mapsurveyor::class, cascade: ['persist', 'remove'], indexBy: 'sequence')]
    private Collection $mapsurveyor;
    ### END ONETOONE PROPERTIES

    public function __construct()
    {
        $this->mapcave = new ArrayCollection();
        $this->mapcitation = new ArrayCollection();
        $this->mapcommentline = new ArrayCollection();//Todo
        $this->mapdrafter = new ArrayCollection();
        $this->mapfurthergc = new ArrayCollection();
        $this->mapfurtherpc = new ArrayCollection();
        $this->mapsurveyor = new ArrayCollection();
        $this->mapimage = new ArrayCollection();
        $this->maplink = new ArrayCollection();
    }

    public function getMaplink(): Collection
    {
        return $this->maplink;
    }

    public function addMaplink(Maplink $maplink): Map
    {
        $this->maplink->add($maplink);
        return $this;
    }

    public function getMapcave(): Collection
    {
        return $this->mapcave;
    }

    public function addMapcave(Mapcave $mapcave): Map
    {
        $this->mapcave->add($mapcave);
        return $this;
    }

    public function getMapcitation(): Collection
    {
        return $this->mapcitation;
    }

    public function addMapcitation(Mapcitation $mapcitation): Map
    {
        $this->mapcitation->add($mapcitation);
        return $this;
    }

    public function getMapcommentline(): Collection
    {
        return $this->mapcommentline;
    }

    public function addMapcommentline(Mapcommentline $mapcommentline): Map
    {
        $this->mapcommentline->add($mapcommentline);
        return $this;
    }    
    
    public function getMapdrafter(): Collection
    {
        return $this->mapdrafter;
    }

    public function addMapdrafter(Mapdrafter $mapdrafter): Map
    {
        $this->mapdrafter->add($mapdrafter);
        return $this;
    }

    public function getMapfurtherpc(): Collection
    {
        return $this->mapfurtherpc;
    }

    public function addMapfurtherpc(Mapfurtherpc $mapfurtherpc): Map
    {
        $this->mapfurtherpc->add($mapfurtherpc);
        return $this;
    }

    public function getMapfurthergc(): Collection
    {
        return $this->mapfurthergc;
    }

    public function addMapfurthergc(Mapfurthergc $mapfurthergc): Map
    {
        $this->mapfurthergc->add($mapfurthergc);
        return $this;
    }

    public function getMapimage(): Collection
    {
        return $this->mapimage;
    }

    public function addMapimage(Mapimage $mapimage): Map
    {
        $this->mapimage->add($mapimage);
        return $this;
    }

    public function getMapsurveyor(): Collection
    {
        return $this->mapsurveyor;
    }

    public function addMapsurveyor(Mapsurveyor $mapsurveyor): Map
    {
        $this->mapsurveyor->add($mapsurveyor);
        return $this;
    }
}