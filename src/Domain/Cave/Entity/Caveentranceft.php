<?php
namespace  App\Domain\Cave\Entity;
use App\Domain\Cave\Entity\Trait\CaveManyToOneTrait;
use App\Domain\Fielddefinition\Entity\Fieldvaluecode;
use  App\Infrastructure\Doctrine\Trait\CrupdatetimeTrait;
use Doctrine\ORM\Mapping as ORM;
use  App\Infrastructure\Doctrine\Trait\SequenceTrait;

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

