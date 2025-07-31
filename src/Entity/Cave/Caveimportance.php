<?php
namespace  App\Entity\Cave;
use App\Domain\Fielddefinition\Entity\Fieldvaluecode;
use App\Entity\Cave\Trait\CaveManyToOneTrait;
use App\Entity\CommonTrait\{CrupdatetimeTrait};
use App\Entity\CommonTrait\SequenceTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * CA0048 Cave importances 0:n
 */
#[ORM\Table(name: 'cave_importance')]
#[ORM\Index(columns: ['cave'], name: 'cave_idx')]
#[ORM\Index(columns: ['cave_importance'], name: 'fieldvaluecode_importance_idx')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]

class Caveimportance
{
    use SequenceTrait, CrupdatetimeTrait, CaveManyToOneTrait;

    /**
      * FD 227
      */
     #[ORM\ManyToOne(targetEntity: Cave::class, inversedBy: 'caveimportance')]
     #[ORM\JoinColumn(name: 'cave', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
     private Cave $cave;

    /**
     * FD 48 Aspects for which this cave or karst feature is important
     */
    #[ORM\ManyToOne(targetEntity: Fieldvaluecode::class)]
    #[ORM\JoinColumn(name: 'cave_importance', referencedColumnName: 'id', nullable: false)]
    private Fieldvaluecode $importance;

    public function getImportance(): ?Fieldvaluecode
    {
        return $this->importance;
    }

    public function setImportance(Fieldvaluecode $importance): Caveimportance
    {
        $this->importance = $importance;
        return $this;
    }
}

