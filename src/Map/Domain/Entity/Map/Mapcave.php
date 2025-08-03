<?php
namespace  App\Map\Domain\Entity\Map;
use App\Cave\Domain\Entity\Cave;
use App\Infrastructure\Doctrine\Trait\CrupdatetimeTrait;
use App\Infrastructure\Doctrine\Trait\SequenceTrait;
use App\Map\Domain\Entity\Map\Model\MapManyToOneInterface;
use App\Map\Domain\Entity\Map\Trait\MapManyToOneTrait;
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

