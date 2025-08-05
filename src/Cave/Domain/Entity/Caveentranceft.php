<?php
namespace  App\Cave\Domain\Entity;
use App\Cave\Domain\Entity\Trait\CaveManyToOneTrait;
use App\Fielddefinition\Domain\Entity\Fieldvaluecode;
use App\Shared\Doctrine\Trait\CrupdatetimeTrait;
use App\Shared\Doctrine\Trait\SequenceTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * CA0532 Entrance feature type 0:n
 */
#[ORM\Table(name: 'cave_entranceft')]
#[ORM\Index(columns: ['cave'], name: 'cave_idx')]
#[ORM\Index(columns: ['cave_entrance'], name: 'fieldvaluecode_cave_entrance_idx')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class Caveentranceft
{
    use SequenceTrait, CrupdatetimeTrait, CaveManyToOneTrait;

    /**
      * FD 227
      */
     #[ORM\ManyToOne(targetEntity: Cave::class, inversedBy: 'caveentranceft')]
     #[ORM\JoinColumn(name: 'cave', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
     private Cave $cave;

    /**
     * FD 532
     */
    #[ORM\ManyToOne(targetEntity: Fieldvaluecode::class)]
    #[ORM\JoinColumn(name: 'cave_entrance', referencedColumnName: 'id', nullable: false)]
    private ?Fieldvaluecode $entrance = null;

    public function getEntrance(): ?Fieldvaluecode
    {
        return $this->entrance;
    }

    public function setEntrance(Fieldvaluecode $entrance): Caveentranceft
    {
        $this->entrance = $entrance;
        return $this;
    }
}

