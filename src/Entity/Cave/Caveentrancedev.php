<?php
namespace  App\Entity\Cave;
use App\Entity\Cave\Trait\CaveManyToOneTrait;
use App\Entity\CommonTrait\{CrupdatetimeTrait};
use App\Entity\CommonTrait\SequenceTrait;
use App\Entity\FieldDefinition\Fieldvaluecode;
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

