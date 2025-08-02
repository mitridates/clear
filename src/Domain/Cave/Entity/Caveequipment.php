<?php
namespace  App\Domain\Cave\Entity;
use App\Domain\Cave\Entity\Trait\CaveManyToOneTrait;
use App\Domain\Fielddefinition\Entity\Fieldvaluecode;
use  App\Infrastructure\Doctrine\Trait\CrupdatetimeTrait;
use Doctrine\ORM\Mapping as ORM;
use  App\Infrastructure\Doctrine\Trait\SequenceTrait;

/**
 * Cave equipment (uncoded) 0:n
 */
#[ORM\Table(name: 'cave_equipment')]
#[ORM\Index(columns: ['cave'], name: 'cave_idx')]
#[ORM\Index(columns: ['priority'], name: 'priority_idx')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class Caveequipment
{
    use SequenceTrait, CrupdatetimeTrait, CaveManyToOneTrait;

    /**
      * FD 227
      */
     #[ORM\ManyToOne(targetEntity: Cave::class, inversedBy: 'caveequipment')]
     #[ORM\JoinColumn(name: 'cave', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
     private Cave $cave;

    /**
     * No FD
     */
    #[ORM\Column(name: 'equipment', type: 'string', length: 62, nullable: false)]
    private ?string $equipment = null;
    /**
     *Priority. Local coded FVC 10003. FD 10383??
     * TODO Por qué no es nullable y sin embargo se puede guardar como null???. Me pasa con varias entidades y no recuerdo por qué lo puse así.
     */
    #[ORM\ManyToOne(targetEntity: Fieldvaluecode::class)]
    #[ORM\JoinColumn(name: 'priority', referencedColumnName: 'id', nullable: false)]
    private ?Fieldvaluecode $priority = null;

    public function getEquipment(): ?string
    {
        return $this->equipment;
    }

    public function setEquipment(string $equipment): Caveequipment
    {
        $this->equipment = $equipment;
        return $this;
    }

    public function getPriority(): ?Fieldvaluecode
    {
        return $this->priority;
    }

    public function setPriority(Fieldvaluecode $priority): Caveequipment
    {
        $this->priority = $priority;
        return $this;
    }
}

