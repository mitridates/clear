<?php
namespace  App\Entity\Cave;
use App\Domain\Fielddefinition\Entity\Fieldvaluecode;
use App\Entity\Cave\Trait\CaveManyToOneTrait;
use CrupdatetimeTrait;
use Doctrine\ORM\Mapping as ORM;
use SequenceTrait;

/**
 * CA0008 Cave type  0:n
 */
#[ORM\Table(name: 'cave_type')]
#[ORM\Index(columns: ['cave'], name: 'cave_idx')]
#[ORM\Index(columns: ['cave_type'], name: 'fieldvaluecode_cave_type_idx')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class Cavetype
{
    use SequenceTrait, CrupdatetimeTrait, CaveManyToOneTrait;

    /**
     * FD 227
     */
    #[ORM\ManyToOne(targetEntity: Cave::class, inversedBy: 'cavetype')]
    #[ORM\JoinColumn(name: 'cave', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private Cave $cave;

    /**
     * FD 8
     */
    #[ORM\ManyToOne(targetEntity: Fieldvaluecode::class)]
    #[ORM\JoinColumn(name: 'cave_type', referencedColumnName: 'id', nullable: false)]
    private Fieldvaluecode $type;

    public function getType(): Fieldvaluecode
    {
        return $this->type;
    }

    public function setType(Fieldvaluecode $type): Cavetype
    {
        $this->type = $type;
        return $this;
    }
}

