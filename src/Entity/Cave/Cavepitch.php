<?php
namespace  App\Entity\Cave;
use App\Entity\Cave\Trait\CaveManyToOneTrait;
use App\Entity\CommonTrait\{CrupdatetimeTrait};
use App\Entity\CommonTrait\SequenceTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * CA0066 Cave pitches 0:n
 */
#[ORM\Table(name: 'cave_pitch')]
#[ORM\Index(columns: ['cave'], name: 'cave_idx')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class Cavepitch
{
    use SequenceTrait, CrupdatetimeTrait, CaveManyToOneTrait;

    /**
      * FD 227
      */
     #[ORM\ManyToOne(targetEntity: Cave::class, inversedBy: 'cavepitch')]
     #[ORM\JoinColumn(name: 'cave', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
     private Cave $cave;

    /**
     * Cave pitch. FD 66
     */
    #[ORM\Column(name: 'pitches', type: 'decimal', precision: 7, scale: 1, nullable: false)]
    private float $pitch;

    public function getPitch(): ?string
    {
        return $this->pitch;
    }

    public function setPitch(float $pitch): Cavepitch
    {
        $this->pitch = $pitch;
        return $this;
    }
}

