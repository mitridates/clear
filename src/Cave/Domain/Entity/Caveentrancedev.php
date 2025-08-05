<?php
namespace  App\Cave\Domain\Entity;
use App\Cave\Domain\Entity\Trait\CaveManyToOneTrait;
use App\Fielddefinition\Domain\Entity\Fieldvaluecode;
use App\Shared\Doctrine\Trait\CrupdatetimeTrait;
use App\Shared\Doctrine\Trait\SequenceTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * CA0533 Entrance development 0:n
 */
#[ORM\Table(name: 'cave_entrancedev')]
#[ORM\Index(columns: ['cave'], name: 'cave_idx')]
#[ORM\Index(columns: ['cave_entrance'], name: 'fieldvaluecode_cave_entrance_idx')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class Caveentrancedev
{
    use SequenceTrait, CrupdatetimeTrait, CaveManyToOneTrait;

    /**
      * FD 227
      */
     #[ORM\ManyToOne(targetEntity: Cave::class, inversedBy: 'caveentrancedev')]
     #[ORM\JoinColumn(name: 'cave', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
     private Cave $cave;

    /**
     * FD 533
     */
    #[ORM\ManyToOne(targetEntity: Fieldvaluecode::class)]
    #[ORM\JoinColumn(name: 'cave_entrance', referencedColumnName: 'id', nullable: false)]
    private ?Fieldvaluecode $entrance = null;

    public function getEntrance(): ?Fieldvaluecode
    {
        return $this->entrance;
    }

    public function setEntrance(Fieldvaluecode $entrance): Caveentrancedev
    {
        $this->entrance = $entrance;
        return $this;
    }
}

