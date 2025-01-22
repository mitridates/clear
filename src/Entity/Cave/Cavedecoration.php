<?php
namespace  App\Entity\Cave;
use App\Entity\Cave\Trait\CaveManyToOneTrait;
use App\Entity\CommonTrait\{CrupdatetimeTrait};
use App\Entity\CommonTrait\SequenceTrait;
use App\Entity\FieldDefinition\Fieldvaluecode;
use Doctrine\ORM\Mapping as ORM;

/**
 * CA0012 Cave Cavedecoration 0:n
 */
#[ORM\Table(name: 'cave_decoration')]
#[ORM\Index(columns: ['cave'], name: 'cave_idx')]
#[ORM\Index(columns: ['decoration'], name: 'fieldvaluecode_decoration_idx')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class Cavedecoration
{
    use SequenceTrait, CrupdatetimeTrait, CaveManyToOneTrait;

    /**
     * FD 227
     */
    #[ORM\ManyToOne(targetEntity: Cave::class, inversedBy: 'cavedecoration')]
    #[ORM\JoinColumn(name: 'cave', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private Cave $cave;

    /**
     * FD 12
     */
    #[ORM\ManyToOne(targetEntity: Fieldvaluecode::class)]
    #[ORM\JoinColumn(name: 'decoration', referencedColumnName: 'id', nullable: false)]
    private ?Fieldvaluecode $decoration = null;

    public function getDecoration(): ?Fieldvaluecode
    {
        return $this->decoration;
    }

    public function setDecoration(Fieldvaluecode $decoration): Cavedecoration
    {
        $this->decoration = $decoration;
        return $this;
    }
}

