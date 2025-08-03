<?php
namespace  App\Cave\Domain\Entity;
use App\Cave\Domain\Entity\Trait\CaveManyToOneTrait;
use App\Fielddefinition\Domain\Entity\Fieldvaluecode;
use App\Infrastructure\Doctrine\Trait\CrupdatetimeTrait;
use App\Infrastructure\Doctrine\Trait\SequenceTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * CA0052 Cave hazard type 0:n
 */
#[ORM\Table(name: 'cave_hazard')]
#[ORM\Index(columns: ['cave'], name: 'cave_idx')]
#[ORM\Index(columns: ['cave_hazard'], name: 'fieldvaluecode_cave_hazard_idx')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class Cavehazard
{
    use SequenceTrait, CrupdatetimeTrait, CaveManyToOneTrait;

    /**
      * FD 227
      */
     #[ORM\ManyToOne(targetEntity: Cave::class, inversedBy: 'cavehazard')]
     #[ORM\JoinColumn(name: 'cave', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
     private Cave $cave;

    /**
     * FD 52
     */
    #[ORM\ManyToOne(targetEntity: Fieldvaluecode::class)]
    #[ORM\JoinColumn(name: 'cave_hazard', referencedColumnName: 'id')]
    private ?Fieldvaluecode $hazard = null;

    public function getHazard(): ?Fieldvaluecode
    {
        return $this->hazard;
    }

    public function setHazard(Fieldvaluecode $hazard): Cavehazard
    {
        $this->hazard = $hazard;
        return $this;
    }
}