<?php
namespace  App\Cave\Domain\Entity;
use App\Cave\Domain\Entity\Trait\CaveManyToOneTrait;
use App\Fielddefinition\Domain\Entity\Fieldvaluecode;
use App\Infrastructure\Doctrine\Trait\CrupdatetimeTrait;
use App\Infrastructure\Doctrine\Trait\SequenceTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * Cave prospect (CA0051) 0:n
 */
#[ORM\Table(name: 'cave_prospect')]
#[ORM\Index(columns: ['cave'], name: 'cave_idx')]
#[ORM\Index(columns: ['cave_prospect'], name: 'fieldvaluecode_prospect_idx')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class Caveprospect
{
    use SequenceTrait, CrupdatetimeTrait, CaveManyToOneTrait;

    /**
      * FD 227
      */
     #[ORM\ManyToOne(targetEntity: Cave::class, inversedBy: 'caveprospect')]
     #[ORM\JoinColumn(name: 'cave', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
     private Cave $cave;

    /**
     * Cave prospect. FD 51
     */
    #[ORM\ManyToOne(targetEntity: Fieldvaluecode::class)]
    #[ORM\JoinColumn(name: 'cave_prospect', referencedColumnName: 'id', nullable: false)]
    private Fieldvaluecode $prospect;

    public function getProspect(): ?Fieldvaluecode
    {
        return $this->prospect;
    }

    public function setProspect(Fieldvaluecode $prospect): Caveprospect
    {
        $this->prospect = $prospect;
        return $this;
    }
}

