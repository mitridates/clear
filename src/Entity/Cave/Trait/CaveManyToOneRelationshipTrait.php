<?php
namespace App\Entity\Cave\Trait;

use App\Entity\Cave\Caveaccess;
use App\Entity\Cave\Cavecomment;
use App\Entity\Cave\Cavecontent;
use App\Entity\Cave\Cavecrossreference;
use App\Entity\Cave\Cavedamage;
use App\Entity\Cave\Cavedecoration;
use App\Entity\Cave\Cavedescriptionline;
use App\Entity\Cave\Cavedevelopment;
use App\Entity\Cave\Cavedifficulty;
use App\Entity\Cave\Cavedirection;
use App\Entity\Cave\Cavediscovery;
use App\Entity\Cave\Caveentrancedev;
use App\Entity\Cave\Caveentranceft;
use App\Entity\Cave\Caveentranceline;
use App\Entity\Cave\Caveequipment;
use App\Entity\Cave\Caveexcluded;
use App\Entity\Cave\Caveextensiondiscovery;
use App\Entity\Cave\Cavegrid;
use App\Entity\Cave\Cavehazard;
use App\Entity\Cave\Caveimportance;
use App\Entity\Cave\Cavelandunit;
use App\Entity\Cave\Cavelink;
use App\Entity\Cave\Cavename;
use App\Entity\Cave\Caveotherdbid;
use App\Entity\Cave\Cavepitch;
use App\Entity\Cave\Cavepreviousnumber;
use App\Entity\Cave\Caveprospect;
use App\Entity\Cave\Caveprotection;
use App\Entity\Cave\Cavereference;
use App\Entity\Cave\Caverocktype;
use App\Entity\Cave\Cavespecie;
use App\Entity\Cave\Cavesurfaceuse;
use App\Entity\Cave\Cavetodo;
use App\Entity\Cave\Cavetype;
use App\Entity\Cave\Caveuse;
use App\Entity\Cave\Cavewidestmap;
use App\Entity\Map\Mapcave;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

trait CaveManyToOneRelationshipTrait
{
    ### ONETOMANY PROPERTIES
    /**
     * @var ArrayCollection CA0258  Cave access 0:n
     */
    #[ORM\OneToMany(mappedBy: 'cave', targetEntity: Caveaccess::class, cascade: ['persist', 'remove'], indexBy: 'sequence')]
    private ArrayCollection $caveaccess;

    /**
     * @var ArrayCollection CA0035  Cave extension discovery 0:n
     */
    #[ORM\OneToMany(mappedBy: 'cave', targetEntity: Caveextensiondiscovery::class, cascade: ['persist', 'remove'], indexBy: 'sequence')]
    private ArrayCollection $caveextensiondiscovery;

    
    /**
     * @var ArrayCollection CA0053
     */
    #[ORM\OneToMany(mappedBy: 'cave', targetEntity: Cavecomment::class, cascade: ['persist', 'remove'], indexBy: 'sequence')]
    private ArrayCollection $cavecomment;

    /**
     * @var ArrayCollection Valuecode 72 CA0072
     */
    #[ORM\OneToMany(mappedBy: 'cave', targetEntity: Cavecontent::class, cascade: ['persist', 'remove'], indexBy: 'sequence')]
    private ArrayCollection $cavecontent;

    /**
     * @var ArrayCollection CA0074
     */
    #[ORM\OneToMany(mappedBy: 'cave', targetEntity: Cavecrossreference::class, cascade: ['persist', 'remove'], indexBy: 'sequence')]
    private ArrayCollection $cavecrossreference;

    /**
     * @var ArrayCollection CA0043 Cave damage 0:n
     */
    #[ORM\OneToMany(mappedBy: 'cave', targetEntity: Cavedamage::class, cascade: ['persist', 'remove'], indexBy: 'sequence')]
    private ArrayCollection $cavedamage;

    /**
     * @var ArrayCollection CA0011 Cave Cavedevelopment 0:n
     */
    #[ORM\OneToMany(mappedBy: 'cave', targetEntity: Cavedevelopment::class, cascade: ['persist', 'remove'], indexBy: 'sequence')]
    private ArrayCollection $cavedevelopment;

    /**
     * @var ArrayCollection CA0012
     */
    #[ORM\OneToMany(mappedBy: 'cave', targetEntity: Cavedecoration::class, cascade: ['persist', 'remove'], indexBy: 'sequence')]
    private ArrayCollection $cavedecoration;

    /**
     * @var ArrayCollection (CA0525)
     */
    #[ORM\OneToMany(mappedBy: 'cave', targetEntity: Cavedescriptionline::class, cascade: ['persist', 'remove'], indexBy: 'sequence')]
    private ArrayCollection $cavedescriptionline;

    /**
     * @var ArrayCollection CA0050
     */
    #[ORM\OneToMany(mappedBy: 'cave', targetEntity: Cavedifficulty::class, cascade: ['persist', 'remove'], indexBy: 'sequence')]
    private ArrayCollection $cavedifficulty;

    /**
     * @var ArrayCollection Directions to find cave (CA0257)
     */
    #[ORM\OneToMany(mappedBy: 'cave', targetEntity: Cavedirection::class, cascade: ['persist', 'remove'], indexBy: 'sequence')]
    private ArrayCollection $cavedirection;

    /**
     * @var ArrayCollection CA0030 Cave discovery
     */
    #[ORM\OneToMany(mappedBy: 'cave', targetEntity: Cavediscovery::class, cascade: ['persist', 'remove'], indexBy: 'sequence')]
    private ArrayCollection $cavediscovery;

    /**
     * @var ArrayCollection CA0533 Entrance development 0:n
     */
    #[ORM\OneToMany(mappedBy: 'cave', targetEntity: Caveentrancedev::class, cascade: ['persist', 'remove'], indexBy: 'sequence')]
    private ArrayCollection $caveentrancedev;

    /**
     * @var ArrayCollection CA0532 Entrance feature type 0:n
     */
    #[ORM\OneToMany(mappedBy: 'cave', targetEntity: Caveentranceft::class, cascade: ['persist', 'remove'], indexBy: 'sequence')]
    private ArrayCollection $caveentranceft;

    /**
     * @var ArrayCollection CA0535 Entrance description (lines) 0:n
     */
    #[ORM\OneToMany(mappedBy: 'cave', targetEntity: Caveentranceline::class, cascade: ['persist', 'remove'], indexBy: 'sequence')]
    private ArrayCollection $caveentranceline;

    /**
     * @var ArrayCollection Equipment (lines) (uncoded) 0:n
     */
    #[ORM\OneToMany(mappedBy: 'cave', targetEntity: Caveequipment::class, cascade: ['persist', 'remove'], indexBy: 'sequence')]
    private ArrayCollection $caveequipment;

    /**
     * @var ArrayCollection CA0075 Fields to be excluded from cave lists 0:n
     */
    #[ORM\OneToMany(mappedBy: 'cave', targetEntity: Caveexcluded::class, cascade: ['persist', 'remove'], indexBy: 'sequence')]
    private ArrayCollection $caveexcluded;

    /**
     * @var ArrayCollection CA0241 == Position on maps
     */
    #[ORM\OneToMany(mappedBy: 'cave', targetEntity: Cavegrid::class, cascade: ['persist', 'remove'], indexBy: 'sequence')]
    private ArrayCollection $cavegrid;

    /**
     * @var ArrayCollection CA0052
     */
    #[ORM\OneToMany(mappedBy: 'cave', targetEntity: Cavehazard::class, cascade: ['persist', 'remove'], indexBy: 'sequence')]
    private ArrayCollection$cavehazard;

    /**
     * @var ArrayCollection CA0048 Cave importance 0:n
     */
    #[ORM\OneToMany(mappedBy: 'cave', targetEntity: Caveimportance::class, cascade: ['persist', 'remove'], indexBy: 'sequence')]
    private ArrayCollection $caveimportance;

    /**
     * @var ArrayCollection CA0439 Cave publishable land unit location 0:n
     */
    #[ORM\OneToMany(mappedBy: 'cave', targetEntity: Cavelandunit::class, cascade: ['persist', 'remove'], indexBy: 'sequence')]
    private ArrayCollection $cavelandunit;

    /**
     * @var ArrayCollection CA0069 Cave other name  0:n
     */
    #[ORM\OneToMany(mappedBy: 'cave', targetEntity: Cavename::class, cascade: ['persist', 'remove'], indexBy: 'sequence')]
    #[ORM\OrderBy(['position' => 'ASC', 'updated' => 'DESC'])]
    private ArrayCollection $cavename;

    /**
     * @var ArrayCollection
     */
    #[ORM\OneToMany(mappedBy: 'cave', targetEntity: Caveotherdbid::class, cascade: ['persist', 'remove'], indexBy: 'sequence')]
    private ArrayCollection $caveotherdbid;

    /**
     * @var ArrayCollection CA0066 Cave pitches 0:n
     */
    #[ORM\OneToMany(mappedBy: 'cave', targetEntity: Cavepitch::class, cascade: ['persist', 'remove'], indexBy: 'sequence')]
    private ArrayCollection $cavepitch;

    /**
     * @var ArrayCollection CA0231 Previous cave numbers 0:n
     */
    #[ORM\OneToMany(mappedBy: 'cave', targetEntity: Cavepreviousnumber::class, cascade: ['persist', 'remove'], indexBy: 'sequence')]
    private ArrayCollection $cavepreviousnumber;

    /**
     * @var ArrayCollection CA0051
     */
    #[ORM\OneToMany(mappedBy: 'cave', targetEntity: Caveprospect::class, cascade: ['persist', 'remove'], indexBy: 'sequence')]
    private ArrayCollection $caveprospect;

    /**
     * @var ArrayCollection
     */
    #[ORM\OneToMany(mappedBy: 'cave', targetEntity: Caveprotection::class, cascade: ['persist', 'remove'], indexBy: 'sequence')]
    private ArrayCollection $caveprotection;

    /**
     * @var ArrayCollection
     */
    #[ORM\OneToMany(mappedBy: 'cave', targetEntity: Cavespecie::class, cascade: ['persist', 'remove'], indexBy: 'sequence')]
    private ArrayCollection $cavespecie;

    /**
     * @var ArrayCollection CA0049 Surface use type 0:n
     */
    #[ORM\OneToMany(mappedBy: 'cave', targetEntity: Cavesurfaceuse::class, cascade: ['persist', 'remove'], indexBy: 'sequence')]
    private ArrayCollection $cavesurfaceuse;

    /**
     * @var ArrayCollection To do actions (lines) (uncoded)
     */
    #[ORM\OneToMany(mappedBy: 'cave', targetEntity: Cavetodo::class, cascade: ['persist', 'remove'], indexBy: 'sequence')]
    private ArrayCollection $cavetodo;

    /**
     * @var ArrayCollection CA0008 Cave type  0:n
     */
    #[ORM\OneToMany(mappedBy: 'cave', targetEntity: Cavetype::class, cascade: ['persist', 'remove'], indexBy: 'sequence')]
    private ArrayCollection $cavetype;

    /**
     * @var ArrayCollection CA0071 Cave reference  0:n
     */
    #[ORM\OneToMany(mappedBy: 'cave', targetEntity: Cavereference::class, cascade: ['persist', 'remove'], indexBy: 'sequence')]
    private ArrayCollection$cavereference;

    /**
     * @var ArrayCollection CA0007 Cave rock type 0:n
     */
    #[ORM\OneToMany(mappedBy: 'cave', targetEntity: Caverocktype::class, cascade: ['persist', 'remove'], indexBy: 'sequence')]
    private ArrayCollection $caverocktype;

    /**
     * @var ArrayCollection CA0041 Cave use 0:n
     */
    #[ORM\OneToMany(mappedBy: 'cave', targetEntity: Caveuse::class, cascade: ['persist', 'remove'], indexBy: 'sequence')]
    private ArrayCollection $caveuse;


    /**
     * @var ArrayCollection Cave widest map, CA0055
     */
    #[ORM\OneToMany(mappedBy: 'cave', targetEntity: Cavewidestmap::class, cascade: ['persist', 'remove'], indexBy: 'sequence')]
    private ArrayCollection $cavewidestmap;

    ### ONETOMANY PROPERTIES
    /**
     * @var ArrayCollection map relationship, PL0601.
     */
    #[ORM\OneToMany(mappedBy: 'cave', targetEntity: Mapcave::class, indexBy: 'sequence')]
    private ArrayCollection $mapcave;


    /**
     * @var ArrayCollection Links
     */
    #[ORM\OneToMany(mappedBy: 'cave', targetEntity: Cavelink::class, cascade: ['persist', 'remove'], indexBy: 'sequence')]
    private ArrayCollection $cavelink;

    public function __construct() {
        $this->caveaccess = new ArrayCollection();
        $this->cavecomment = new ArrayCollection();
        $this->cavecontent = new ArrayCollection();
        $this->cavecrossreference = new ArrayCollection();
        $this->cavedamage = new ArrayCollection();
        $this->cavedecoration = new ArrayCollection();
        $this->cavedescriptionline = new ArrayCollection();
        $this->cavedevelopment = new ArrayCollection();
        $this->cavedifficulty = new ArrayCollection();
        $this->cavedirection = new ArrayCollection();
        $this->cavediscovery = new ArrayCollection();
        $this->caveentrancedev = new ArrayCollection();
        $this->caveentranceft = new ArrayCollection();
        $this->caveentranceline = new ArrayCollection();
        $this->caveequipment = new ArrayCollection();
        $this->caveexcluded = new ArrayCollection();
        $this->cavegrid = new ArrayCollection();
        $this->cavehazard = new ArrayCollection();
        $this->caveimportance = new ArrayCollection();
        $this->cavelandunit = new ArrayCollection();
        $this->cavename = new ArrayCollection();
        $this->caveotherdbid = new ArrayCollection();
        $this->cavepitch = new ArrayCollection();
        $this->cavepreviousnumber = new ArrayCollection();
        $this->caveprospect = new ArrayCollection();
        $this->caveprotection = new ArrayCollection();
        $this->cavereference = new ArrayCollection();
        $this->caverocktype = new ArrayCollection();
        $this->cavespecie = new ArrayCollection();
        $this->cavesurfaceuse = new ArrayCollection();
        $this->cavetodo = new ArrayCollection();
        $this->cavetype = new ArrayCollection();
        $this->caveuse = new ArrayCollection();
        $this->cavewidestmap = new ArrayCollection();
        $this->mapcave = new ArrayCollection();
        $this->cavelink = new ArrayCollection();
    }

    public function getCavelink(): Collection
    {
        return $this->cavelink;
    }

    public function addCavelink(Cavelink $cavelink): Cave
    {
        $this->cavelink->add($cavelink);
        return $this;
    }

    public function getMapcave(): Collection
    {
        return $this->mapcave;
    }

    public function getCaveaccess(): Collection
    {
        return $this->caveaccess;
    }

    public function addCaveaccess(Caveaccess $caveaccess): Cave
    {
        $this->caveaccess->add($caveaccess);
        return $this;
    }

    public function getCavecomment(): Collection
    {
        return $this->cavecomment;
    }

    public function addCaveextensiondiscovery(Caveextensiondiscovery $caveextensiondiscovery): Cave
    {
        $this->caveextensiondiscovery->add($caveextensiondiscovery);
        return $this;
    }

    public function getCaveextensiondiscovery(): Collection
    {
        return $this->caveextensiondiscovery;
    }

    public function addCavecomment(Cavecomment $cavecomment): Cave
    {
        $this->cavecomment->add($cavecomment);
        return $this;
    }
     public function getCavecontent(): Collection
    {
        return $this->cavecontent;
    }

    public function addCavecontent(Cavecontent $cavecontent): Cave
    {
        $this->cavecontent->add($cavecontent);
        return $this;
    }

    public function getCavecrossreference(): Collection
    {
        return $this->cavecrossreference;
    }

    public function addCavecrossreference(Cavecrossreference $cavecrossreference): Cave
    {
        $this->cavecrossreference->add($cavecrossreference);
        return $this;
    }

    public function getCavedamage(): Collection
    {
        return $this->cavedamage;
    }

    public function addCavedamage(Cavedamage $cavedamage): Cave
    {
        $this->cavedamage->add($cavedamage);
        return $this;
    }

    public function getCavedevelopment(): Collection
    {
        return $this->cavedevelopment;
    }

    public function addCavedevelopment(Cavedevelopment $cavedevelopment): Cave
    {
        $this->cavedevelopment->add($cavedevelopment);
        return $this;
    }

    public function getCavedecoration(): Collection
    {
        return $this->cavedecoration;
    }

    public function addCavedecoration(Cavedecoration $cavedecoration): Cave
    {
        $this->cavedecoration->add($cavedecoration);
        return $this;
    }

    public function getCavedescriptionline(): Collection
    {
        return $this->cavedescriptionline;
    }

    public function addCavedescriptionline(Cavedescriptionline $cavedescriptionline): Cave
    {
        $this->cavedescriptionline->add($cavedescriptionline);
        return $this;
    }

    public function getCavedifficulty(): Collection
    {
        return $this->cavedifficulty;
    }

    public function addCavedifficulty(Cavedifficulty $cavedifficulty): Cave
    {
        $this->cavedifficulty->add($cavedifficulty);
        return $this;
    }

    public function getCavedirection(): Collection
    {
        return $this->cavedirection;
    }

    public function addCavedirection(Cavedirection $cavedirection): Cave
    {
        $this->cavedirection->add($cavedirection);
        return $this;
    }

    public function getCavediscovery(): Collection
    {
        return $this->cavediscovery;
    }

    public function addCavediscovery(Cavediscovery $cavediscovery): Cave
    {
        $this->cavediscovery->add($cavediscovery);
        return $this;
    }

    public function getCaveentrancedev(): Collection
    {
        return $this->caveentrancedev;
    }

    public function addCaveentrancedev(Caveentrancedev $caveentrancedev): Cave
    {
        $this->caveentrancedev->add($caveentrancedev);
        return $this;
    }

    public function getCaveentranceft(): Collection
    {
        return $this->caveentranceft;
    }

    public function addCaveentranceft(Caveentranceft $caveentranceft): Cave
    {
        $this->caveentranceft->add($caveentranceft);
        return $this;
    }

    public function getCaveentranceline(): Collection
    {
        return $this->caveentranceline;
    }

    public function addCaveentranceline(Caveentranceline $caveentranceline): Cave
    {
        $this->caveentranceline->add($caveentranceline);
        return $this;
    }

    public function getCaveequipment(): Collection
    {
        return $this->caveequipment;
    }

    public function addCaveequipment(Caveequipment $caveequipment): Cave
    {
        $this->caveequipment->add($caveequipment);
        return $this;
    }

    public function getCaveexcluded(): Collection
    {
        return $this->caveexcluded;
    }

    public function addCaveexcluded(Caveexcluded $caveexcluded): Cave
    {
        $this->caveexcluded->add($caveexcluded);
        return $this;
    }

    public function getCavegrid(): Collection
    {
        return $this->cavegrid;
    }

    public function addCavegrid(Cavegrid $cavegrid): Cave
    {
        $this->cavegrid->add($cavegrid);
        return $this;
    }

    public function getCavehazard(): Collection
    {
        return $this->cavehazard;
    }

    public function addCavehazard(Cavehazard $cavehazard): Cave
    {
        $this->cavehazard->add($cavehazard);
        return $this;
    }

    public function getCaveimportance(): Collection
    {
        return $this->caveimportance;
    }

    public function addCaveimportance(Caveimportance $caveimportance): Cave
    {
        $this->caveimportance->add($caveimportance);
        return $this;
    }

    public function getCavelandunit(): Collection
    {
        return $this->cavelandunit;
    }

    public function addCavelandunit(Cavelandunit $cavelandunit): Cave
    {
        $this->cavelandunit->add($cavelandunit);
        return $this;
    }

    public function getCavename(): Collection
    {
        return $this->cavename;
    }

    public function addCavename(Cavename $cavename): Cave
    {
        $this->cavename->add($cavename);
        return $this;
    }

    public function getCaveotherdbid(): Collection
    {
        return $this->caveotherdbid;
    }

    public function addCaveotherdbid(Caveotherdbid $caveotherdbid): Cave
    {
        $this->caveotherdbid->add($caveotherdbid);
        return $this;
    }

    public function getCavepitch(): Collection
    {
        return $this->cavepitch;
    }

    public function addCavepitch(Cavepitch $cavepitch): Cave
    {
        $this->cavepitch->add($cavepitch);
        return $this;
    }

    public function getCavepreviousnumber(): Collection
    {
        return $this->cavepreviousnumber;
    }

    public function addCavepreviousnumber(Cavepreviousnumber $cavepreviousnumber): Cave
    {
        $this->cavepreviousnumber->add($cavepreviousnumber);
        return $this;
    }

    public function getCaveprospect(): Collection
    {
        return $this->caveprospect;
    }

    public function addCaveprospect(Caveprospect $caveprospect): Cave
    {
        $this->caveprospect->add($caveprospect);
        return $this;
    }

    public function getCaveprotection(): Collection
    {
        return $this->caveprotection;
    }

    public function addCaveprotection(Caveprotection $caveprotection): Cave
    {
        $this->caveprotection->add($caveprotection);
        return $this;
    }

    public function getCavespecie(): Collection
    {
        return $this->cavespecie;
    }

    public function addCavespecie(Cavespecie $cavespecie): Cave
    {
        $this->cavespecie->add($cavespecie);
        return $this;
    }

    public function getCavesurfaceuse(): Collection
    {
        return $this->cavesurfaceuse;
    }

    public function addCavesurfaceuse(Cavesurfaceuse $cavesurfaceuse): Cave
    {
        $this->cavesurfaceuse->add($cavesurfaceuse);
        return $this;
    }

    public function getCavetodo(): Collection
    {
        return $this->cavetodo;
    }

    public function addCavetodo(Cavetodo $cavetodo): Cave
    {
        $this->cavetodo->add($cavetodo);
        return $this;
    }

    public function getCavetype(): Collection
    {
        return $this->cavetype;
    }

    public function addCavetype(Cavetype $cavetype): Cave
    {
        $this->cavetype->add($cavetype);
        return $this;
    }

    public function getCavereference(): Collection
    {
        return $this->cavereference;
    }

    public function addCavereference(Cavereference $cavereference): Cave
    {
        $this->cavereference->add($cavereference);
        return $this;
    }

    public function getCaverocktype(): Collection
    {
        return $this->caverocktype;
    }

    public function addCaverocktype(Caverocktype $caverocktype): Cave
    {
        $this->caverocktype->add($caverocktype);
        return $this;
    }

    public function getCaveuse(): Collection
    {
        return $this->caveuse;
    }

    public function addCaveuse(Caveuse $caveuse): Cave
    {
        $this->caveuse->add($caveuse);
        return $this;
    }

    public function getCavewidestmap(): Collection
    {
        return $this->cavewidestmap;
    }

    public function addCavewidestmap(Cavewidestmap $cavewidestmap): Cave
    {
        $this->cavewidestmap->add($cavewidestmap);
        return $this;
    }
}