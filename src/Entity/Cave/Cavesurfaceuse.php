<?php
namespace  App\Entity\Cave;
use App\Domain\Fielddefinition\Entity\Fieldvaluecode;
use App\Entity\Cave\Trait\CaveManyToOneTrait;
use CrupdatetimeTrait;
use Doctrine\ORM\Mapping as ORM;
use SequenceTrait;

/**
 * CA0049 Surface use type 0:n
 */
#[ORM\Table(name: 'cave_surfaceuse')]
#[ORM\Index(columns: ['cave'], name: 'cave_idx')]
#[ORM\Index(columns: ['cave_surfaceuse'], name: 'fieldvaluecode_surfaceuse_idx')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class Cavesurfaceuse
{
    use SequenceTrait, CrupdatetimeTrait, CaveManyToOneTrait;

    /**
     * FD 227
     */
    #[ORM\ManyToOne(targetEntity: Cave::class, inversedBy: 'cavesurfaceuse')]
    #[ORM\JoinColumn(name: 'cave', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private Cave $cave;

    /**
     * FD 49
     */
    #[ORM\ManyToOne(targetEntity: Fieldvaluecode::class)]
    #[ORM\JoinColumn(name: 'cave_surfaceuse', referencedColumnName: 'id', nullable: false)]
    private Fieldvaluecode $surfaceuse;

    public function getSurfaceuse(): Fieldvaluecode
    {
        return $this->surfaceuse;
    }

    public function setSurfaceuse(Fieldvaluecode $surfaceuse): Cavesurfaceuse
    {
        $this->surfaceuse = $surfaceuse;
        return $this;
    }
}
