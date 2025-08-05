<?php
namespace  App\Cave\Domain\Entity;
use App\Cave\Domain\Entity\Trait\CaveManyToOneTrait;
use App\Shared\Doctrine\Trait\CrupdatetimeTrait;
use App\Shared\Doctrine\Trait\SequenceTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * CA0257 Directions to find cave 0:n
 */
#[ORM\Table(name: 'cave_direction')]
#[ORM\Index(columns: ['cave'], name: 'cave_idx')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class Cavedirection
{
    use SequenceTrait, CrupdatetimeTrait, CaveManyToOneTrait;

    /**
     * FD 227
     */
    #[ORM\ManyToOne(targetEntity: Cave::class, inversedBy: 'cavedirection')]
    #[ORM\JoinColumn(name: 'cave', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private Cave $cave;

    /**
     * FD 257
     */
    #[ORM\Column(name: 'directions_to_find_cave', type: 'string', length: 62, nullable: false)]
    private ?string $direction = null;

    public function getDirection(): string
    {
        return $this->direction;
    }

    public function setDirection(string $direction): Cavedirection
    {
        $this->direction = $direction;
        return $this;
    }
}

