<?php
namespace  App\Cave\Domain\Entity;
use App\Cave\Domain\Entity\Trait\CaveManyToOneTrait;
use App\Shared\Doctrine\Trait\CrupdatetimeTrait;
use App\Shared\Doctrine\Trait\SequenceTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * CA0439 Cave publishable land unit location 0:n
 */
#[ORM\Table(name: 'cave_landunit')]
#[ORM\Index(columns: ['cave'], name: 'cave_idx')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class Cavelandunit
{
    use SequenceTrait, CrupdatetimeTrait, CaveManyToOneTrait;

    /**
      * FD 227
      */
     #[ORM\ManyToOne(targetEntity: Cave::class, inversedBy: 'cavelandunit')]
     #[ORM\JoinColumn(name: 'cave', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
     private Cave $cave;

    /**
     * FD 439
     */
    #[ORM\Column(name: 'land_unit_coarse', type: 'string', length: 30, nullable: false)]
    private string $landunit;

    public function getLandunit(): ?string
    {
        return $this->landunit;
    }

    public function setLandunit(string $landunit): Cavelandunit
    {
        $this->landunit = $landunit;
        return $this;
    }
}

