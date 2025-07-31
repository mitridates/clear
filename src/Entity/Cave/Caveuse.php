<?php
namespace  App\Entity\Cave;
use App\Domain\Fielddefinition\Entity\Fieldvaluecode;
use App\Entity\Cave\Trait\CaveManyToOneTrait;
use App\Entity\CommonTrait\{CrupdatetimeTrait};
use App\Entity\CommonTrait\SequenceTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * CA0041 Cave use 0:n
 */
#[ORM\Table(name: 'cave_use')]
#[ORM\Index(columns: ['cave'], name: 'cave_idx')]
#[ORM\Index(columns: ['cave_use'], name: 'fieldvaluecode_cave_use_idx')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class Caveuse
{
    use SequenceTrait, CrupdatetimeTrait, CaveManyToOneTrait;

    /**
     * FD 227
     */
    #[ORM\ManyToOne(targetEntity: Cave::class, inversedBy: 'caveuse')]
    #[ORM\JoinColumn(name: 'cave', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private Cave $cave;

    /**
     * FD 41
     */
    #[ORM\ManyToOne(targetEntity: Fieldvaluecode::class)]
    #[ORM\JoinColumn(name: 'cave_use', referencedColumnName: 'id', nullable: false)]
    private Fieldvaluecode $use;

    public function getUse(): Fieldvaluecode
    {
        return $this->use;
    }

    public function setUse(Fieldvaluecode $use): Caveuse
    {
        $this->use = $use;
        return $this;
    }
}
