<?php
namespace  App\Cave\Domain\Entity;
use App\Cave\Domain\Entity\Trait\CaveOneToOneTrait;
use App\Shared\Doctrine\Trait\CrupdatetimeTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * CA0680 Cave how to find 0:1
 */
#[ORM\Table(name: 'cave_howtofind')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class Cavehowtofind
{
    use CaveOneToOneTrait, CrupdatetimeTrait;

    /**
      *  FD 227
      */
     #[ORM\Id]
     #[ORM\GeneratedValue(strategy: 'NONE')]
     #[ORM\OneToOne(inversedBy: 'cavehowtofind', targetEntity: Cave::class)]
     #[ORM\JoinColumn(name: 'cave', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
     private Cave $cave;

    /**
     * FD 680. How to find cave - memo
     */
    #[ORM\Column(name: 'howto_find_cave', type: 'text', nullable: false)]
    private string $howtofind;

    public function getHowtofind(): string
    {
        return $this->howtofind;
    }

    public function setHowtofind(string $howtofind): Cavehowtofind
    {
        $this->howtofind = $howtofind;
        return $this;
    }
}
