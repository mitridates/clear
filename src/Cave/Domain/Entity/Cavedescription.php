<?php
namespace  App\Cave\Domain\Entity;
use App\Cave\Domain\Entity\Trait\CaveOneToOneTrait;
use App\Infrastructure\Doctrine\Trait\CrupdatetimeTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * CA0530 Description of the cave 0:1
 */
#[ORM\Table(name: 'cave_description')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class Cavedescription
{
    use CaveOneToOneTrait, CrupdatetimeTrait;

    /**
      * FD 227
      */
     #[ORM\Id]
     #[ORM\GeneratedValue(strategy: 'NONE')]
     #[ORM\OneToOne(inversedBy: 'cavedescription', targetEntity: Cave::class)]
     #[ORM\JoinColumn(name: 'cave', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
     private Cave $cave;

    /**
     * FD 680. How to find cave - memo
     */
    #[ORM\Column(name: 'description', type: 'text', nullable: true)]
    private ?string $description = null;

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): Cavedescription
    {
        $this->description = $description;
        return $this;
    }
}
