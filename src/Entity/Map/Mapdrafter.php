<?php
namespace  App\Entity\Map;
use App\Entity\Map\Model\MapManyToOneInterface;
use App\Entity\CommonTrait\{CrupdatetimeTrait};
use App\Entity\CommonTrait\SequenceTrait;
use App\Entity\Map\Trait\MapManyToOneTrait;
use App\Entity\Person;
use Doctrine\ORM\Mapping as ORM;

/**
 * Drafter (PL0587)
 */
#[ORM\Table(name: 'map_drafter')]
#[ORM\Index(columns: ['map'], name: 'map_idx')]
#[ORM\Index(columns: ['map_drafter_id'], name: 'map_person_drafter_idx')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class Mapdrafter implements MapManyToOneInterface
{
    use SequenceTrait, CrupdatetimeTrait, MapManyToOneTrait;

    /**
      * FD 195
      */
     #[ORM\ManyToOne(targetEntity:  Map::class, inversedBy: 'mapdrafter')]
     #[ORM\JoinColumn(name: 'map', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
     private $map;

    /**
     * Map drafter. FD 585
     */
    #[ORM\Column(name: 'map_drafter', type: 'string', length: 25, nullable: true)]
    private ?string $drafter = null;

    /**
     * Map drafter ID. FD 587
     */
    #[ORM\ManyToOne(targetEntity: Person::class)]
    #[ORM\JoinColumn(name: 'map_drafter_id', referencedColumnName: 'id', nullable: true)]
    private ?Person $drafterid = null;

    public function setDrafterid(?Person $drafterid): Mapdrafter
    {
        $this->drafterid = $drafterid;
        return $this;
    }

    public function getDrafterid(): ?Person
    {
        return $this->drafterid;
    }

    public function setDrafter(?string $drafter):Mapdrafter
    {
        $this->drafter = $drafter;
        return $this;
    }

    public function getDrafter(): ?string
    {
        return $this->drafter;
    }    
}

