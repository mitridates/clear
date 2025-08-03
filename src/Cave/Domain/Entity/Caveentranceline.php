<?php
namespace  App\Cave\Domain\Entity;
use App\Cave\Domain\Entity\Trait\CaveManyToOneTrait;
use App\Infrastructure\Doctrine\Trait\CrupdatetimeTrait;
use App\Infrastructure\Doctrine\Trait\SequenceTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * CA0257 Entrance lines to find cave  0:n
 */
#[ORM\Table(name: 'cave_entranceline')]
#[ORM\Index(columns: ['cave'], name: 'cave_idx')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class Caveentranceline
{
    use SequenceTrait, CrupdatetimeTrait, CaveManyToOneTrait;

    /**
      * FD 227
      */
     #[ORM\ManyToOne(targetEntity: Cave::class, inversedBy: 'caveentranceline')]
     #[ORM\JoinColumn(name: 'cave', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
     private Cave $cave;

    /**
     * FD 257
     */
    #[ORM\Column(name: 'entranceline', type: 'string', length: 62, nullable: false)]
    private ?string $entranceline = null;

    public function getEntranceline(): ?string
    {
        return $this->entranceline;
    }

    public function setEntranceline(string $entranceline): Caveentranceline
    {
        $this->entranceline = $entranceline;
        return $this;
    }
}

