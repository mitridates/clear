<?php
namespace  App\Cave\Domain\Entity;
use App\Cave\Domain\Entity\Trait\CaveManyToOneTrait;
use App\Fielddefinition\Domain\Entity\Fieldvaluecode;
use App\Shared\Doctrine\Trait\CrupdatetimeTrait;
use App\Shared\Doctrine\Trait\SequenceTrait;
use Doctrine\ORM\Mapping as ORM;

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

