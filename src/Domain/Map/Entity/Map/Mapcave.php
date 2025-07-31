<?php
namespace  App\Domain\Map\Entity\Map;
use App\Domain\Map\Entity\Map\Model\MapManyToOneInterface;
use App\Domain\Map\Entity\Map\Trait\MapManyToOneTrait;
use App\Entity\Cave\Cave;
use App\Entity\CommonTrait\CrupdatetimeTrait;
use App\Entity\CommonTrait\SequenceTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * Caves on map (PL0587)
 */
#[ORM\Table(name: 'map_cave')]
#[ORM\Index(columns: ['map'], name: 'map_idx')]
#[ORM\Index(columns: ['cave'], name: 'map_cave_idx')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class Mapcave implements MapManyToOneInterface
{
    use SequenceTrait, CrupdatetimeTrait, MapManyToOneTrait;

    /**
      * FD 195
      */
     #[ORM\ManyToOne(targetEntity:  Map::class, inversedBy: 'mapcave')]
     #[ORM\JoinColumn(name: 'map', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
     private $map;

    /**
     * @var Cave FD 227
     */
    #[ORM\ManyToOne(targetEntity: Cave::class, inversedBy: 'mapcave')]
    #[ORM\JoinColumn(name: 'cave', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private Cave $cave;

    /**
     * @param $cave
     * @return Mapcave
     */
    public function setCave($cave): Mapcave
    {
        $this->cave = $cave;
        return $this;
    }

    /**
     * @return ?Cave
     */
    public function getCave(): ?Cave
    {
        return $this->cave;
    }
}

