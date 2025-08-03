<?php
namespace  App\Cave\Domain\Entity;
use App\Cave\Domain\Entity\Trait\CaveManyToOneTrait;
use App\Infrastructure\Doctrine\Trait\CrupdatetimeTrait;
use App\Infrastructure\Doctrine\Trait\SequenceTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * CA0525 Description of cave (lines)  0:n
 */
#[ORM\Table(name: 'cave_descriptionline')]
#[ORM\Index(columns: ['cave'], name: 'cave_idx')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class Cavedescriptionline
{
    use SequenceTrait, CrupdatetimeTrait, CaveManyToOneTrait;

    /**
      * FD 227
      */
     #[ORM\ManyToOne(targetEntity: Cave::class, inversedBy: 'cavedescriptionline')]
     #[ORM\JoinColumn(name: 'cave', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
     private Cave $cave;

    /**
     * FD 525
     */
    #[ORM\Column(name: 'descriptionline', type: 'string', length: 62, nullable: true)]
    private ?string $descriptionline = null;

    public function getDescriptionline(): string
    {
        return $this->descriptionline;
    }

    public function setDescriptionline(string $descriptionline): Cavedescriptionline
    {
        $this->descriptionline = $descriptionline;
        return $this;
    }
}

