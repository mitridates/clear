<?php
namespace  App\Entity\Cave;
use App\Entity\Cave\Trait\CaveOneToOneTrait;
use CrupdatetimeTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * CA0534 Entrance description (memo)
 */
#[ORM\Table(name: 'cave_entrancememo')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class Caveentrancememo
{
    use CaveOneToOneTrait, CrupdatetimeTrait;

    /**
      *  FD 227
      */
     #[ORM\Id]
     #[ORM\GeneratedValue(strategy: 'NONE')]
     #[ORM\OneToOne(inversedBy: 'caveentrancememo', targetEntity: Cave::class)]
     #[ORM\JoinColumn(name: 'cave', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
     private Cave $cave;

    /**
     * FD 534
     */
    #[ORM\Column(name: 'entrancememo', type: 'text', nullable: false)]
    private ?string $entrancememo = null;

    public function getEntrancememo(): string
    {
        return $this->entrancememo;
    }

    public function setEntrancememo(string $entrancememo): Caveentrancememo
    {
        $this->entrancememo = $entrancememo;
        return $this;
    }
}
