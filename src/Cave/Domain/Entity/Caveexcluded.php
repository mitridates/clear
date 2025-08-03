<?php
namespace  App\Cave\Domain\Entity;
use App\Cave\Domain\Entity\Trait\CaveManyToOneTrait;
use App\Fielddefinition\Domain\Entity\Fielddefinition;
use App\Infrastructure\Doctrine\Trait\CrupdatetimeTrait;
use App\Infrastructure\Doctrine\Trait\SequenceTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * CA0075 Fields to be excluded from cave lists 0:n
 */
#[ORM\Table(name: 'cave_excluded')]
#[ORM\Index(columns: ['cave'], name: 'cave_idx')]
#[ORM\Index(columns: ['cave_list_exclusion_fid'], name: 'excluded_idx')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class Caveexcluded
{
    use SequenceTrait, CrupdatetimeTrait, CaveManyToOneTrait;

    /**
     * FD 227
     */
    #[ORM\ManyToOne(targetEntity: Cave::class, inversedBy: 'caveexcluded')]
    #[ORM\JoinColumn(name: 'cave', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private Cave $cave;

    /**
     * FD 75
     */
    #[ORM\ManyToOne(targetEntity:  Fielddefinition::class)]
    #[ORM\JoinColumn(name: 'cave_list_exclusion_fid', referencedColumnName: 'id', nullable: false)]
    private Fielddefinition $excluded;

    public function getExcluded(): Fielddefinition
    {
        return $this->excluded;
    }

    public function setExcluded(Fielddefinition $excluded): Caveexcluded
    {
        $this->excluded = $excluded;
        return $this;
    }
}

