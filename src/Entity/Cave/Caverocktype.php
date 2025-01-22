<?php
namespace  App\Entity\Cave;
use App\Entity\Cave\Trait\CaveManyToOneTrait;
use App\Entity\CommonTrait\{CrupdatetimeTrait};
use App\Entity\CommonTrait\SequenceTrait;
use App\Entity\FieldDefinition\Fieldvaluecode;
use Doctrine\ORM\Mapping as ORM;

/**
 * CA0007 Cave rock type 0:n
 */
#[ORM\Table(name: 'cave_rocktype')]
#[ORM\Index(columns: ['cave'], name: 'cave_idx')]
#[ORM\Index(columns: ['rock_type'], name: 'fieldvaluecode_rock_type_idx')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class Caverocktype
{
    use SequenceTrait, CrupdatetimeTrait, CaveManyToOneTrait;

    /**
     * FD 227
     */
    #[ORM\ManyToOne(targetEntity: Cave::class, inversedBy: 'caverocktype')]
    #[ORM\JoinColumn(name: 'cave', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private Cave $cave;

    /**
     * FD 7
     */
    #[ORM\ManyToOne(targetEntity: Fieldvaluecode::class)]
    #[ORM\JoinColumn(name: 'rock_type', referencedColumnName: 'id', nullable: false)]
    private Fieldvaluecode $rocktype;

    public function getRocktype(): Fieldvaluecode
    {
        return $this->rocktype;
    }

    public function setRocktype(Fieldvaluecode $rocktype): Caverocktype
    {
        $this->rocktype = $rocktype;
        return $this;
    }
}
