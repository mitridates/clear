<?php
namespace  App\Domain\Cave\Entity;
use App\Domain\Cave\Entity\Trait\CaveManyToOneTrait;
use App\Domain\Fielddefinition\Entity\Fieldvaluecode;
use  App\Infrastructure\Doctrine\Trait\CrupdatetimeTrait;
use Doctrine\ORM\Mapping as ORM;
use  App\Infrastructure\Doctrine\Trait\SequenceTrait;

/**
 * CA0043 Cave damage 0:n
 */
#[ORM\Table(name: 'cave_damage')]
#[ORM\Index(columns: ['cave'], name: 'cave_idx')]
#[ORM\Index(columns: ['cave_damage'], name: 'fieldvaluecode_cave_damage_idx')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class Cavedamage
{
    use SequenceTrait, CrupdatetimeTrait, CaveManyToOneTrait;

    /**
      * FD 227
      */
     #[ORM\ManyToOne(targetEntity: Cave::class, inversedBy: 'cavedamage')]
     #[ORM\JoinColumn(name: 'cave', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
     private Cave $cave;

    /**
     *  FD 41
     */
    #[ORM\ManyToOne(targetEntity: Fieldvaluecode::class)]
    #[ORM\JoinColumn(name: 'cave_damage', referencedColumnName: 'id')]
    private ?Fieldvaluecode $damage = null;

    public function getDamage(): ?Fieldvaluecode
    {
        return $this->damage;
    }

    public function setDamage(Fieldvaluecode $damage): Cavedamage
    {
        $this->damage = $damage;
        return $this;
    }
}

