<?php
namespace  App\Entity\Cave;
use App\Entity\Cave\Trait\CaveManyToOneTrait;
use CrupdatetimeTrait;
use Doctrine\ORM\Mapping as ORM;
use SequenceTrait;

/**
 * CA0069 Cave other name  0:n
 */
#[ORM\Table(name: 'cave_name')]
#[ORM\Index(columns: ['cave'], name: 'cave_idx')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class Cavename
{
        use SequenceTrait, CrupdatetimeTrait, CaveManyToOneTrait;

    /**
      * FD 227
      */
     #[ORM\ManyToOne(targetEntity: Cave::class, inversedBy: 'cavename')]
     #[ORM\JoinColumn(name: 'cave', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
     private Cave $cave;

    /**
     * FD 69
     */
    #[ORM\Column(name: 'other_names', type: 'string', length: 52, nullable: false)]
    private string $name;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): Cavename
    {
        $this->name = $name;
        return $this;
    }
}

