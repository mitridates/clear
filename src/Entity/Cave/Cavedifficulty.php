<?php
namespace  App\Entity\Cave;
use App\Domain\Fielddefinition\Entity\Fieldvaluecode;
use App\Entity\Cave\Trait\CaveManyToOneTrait;
use App\Entity\CommonTrait\{CrupdatetimeTrait};
use App\Entity\CommonTrait\SequenceTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * CA0050 Cave difficulty 0:n
 */
#[ORM\Table(name: 'cave_difficulty')]
#[ORM\Index(columns: ['cave'], name: 'cave_idx')]
#[ORM\Index(columns: ['cave_difficulty'], name: 'fieldvaluecode_difficulty_idx')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class Cavedifficulty
{
    use SequenceTrait, CrupdatetimeTrait, CaveManyToOneTrait;

    /**
      * FD 227
      */
     #[ORM\ManyToOne(targetEntity: Cave::class, inversedBy: 'cavedifficulty')]
     #[ORM\JoinColumn(name: 'cave', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
     private Cave $cave;

    /**
     * FD 50
     */
    #[ORM\ManyToOne(targetEntity: Fieldvaluecode::class)]
    #[ORM\JoinColumn(name: 'cave_difficulty', referencedColumnName: 'id', nullable: false)]
    private ?Fieldvaluecode $difficulty = null;

    public function getDifficulty(): ?Fieldvaluecode
    {
        return $this->difficulty;
    }

    public function setDifficulty(Fieldvaluecode $difficulty): Cavedifficulty
    {
        $this->difficulty = $difficulty;
        return $this;
    }
}

