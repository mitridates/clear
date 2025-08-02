<?php
namespace  App\Entity\Cave;
use App\Domain\Fielddefinition\Entity\Fieldvaluecode;
use App\Entity\Cave\Trait\CaveManyToOneTrait;
use CrupdatetimeTrait;
use Doctrine\ORM\Mapping as ORM;
use SequenceTrait;

/**
 * CA0046 Cave protection 0:n
 */
#[ORM\Table(name: 'cave_protection')]
#[ORM\Index(columns: ['cave'], name: 'cave_idx')]
#[ORM\Index(columns: ['cave_protection'], name: 'fieldvaluecode_cave_protection_idx')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class Caveprotection
{
    use SequenceTrait, CrupdatetimeTrait, CaveManyToOneTrait;

    /**
      * FD 227
      */
     #[ORM\ManyToOne(targetEntity: Cave::class, inversedBy: 'caveprotection')]
     #[ORM\JoinColumn(name: 'cave', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
     private Cave $cave;

    /**
     * Cave protection. FD 46
     */
    #[ORM\ManyToOne(targetEntity: Fieldvaluecode::class)]
    #[ORM\JoinColumn(name: 'cave_protection', referencedColumnName: 'id', nullable: false)]
    private ?Fieldvaluecode $protection = null;

    public function getProtection(): ?Fieldvaluecode
    {
        return $this->protection;
    }

    public function setProtection(Fieldvaluecode $protection): Caveprotection
    {
        $this->protection = $protection;
        return $this;
    }
}
