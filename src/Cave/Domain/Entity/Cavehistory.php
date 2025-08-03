<?php
namespace  App\Cave\Domain\Entity;
use App\Cave\Domain\Entity\Trait\CaveOneToOneTrait;
use App\Infrastructure\Doctrine\Trait\CrupdatetimeTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * History of cave (uncoded)
 */
#[ORM\Table(name: 'cave_history')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class Cavehistory
{
    use CaveOneToOneTrait, CrupdatetimeTrait;

    /**
      *  FD 227
      */
     #[ORM\Id]
     #[ORM\GeneratedValue(strategy: 'NONE')]
     #[ORM\OneToOne(inversedBy: 'cavehistory', targetEntity: Cave::class)]
     #[ORM\JoinColumn(name: 'cave', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
     private Cave $cave;

    /**
     * FD 13077 (Local coded) Cave history
     */
    #[ORM\Column(name: 'history', type: 'text', nullable: false)]
    private string $history;

    public function getHistory(): string
    {
        return $this->history;
    }

    public function setHistory(string $history): Cavehistory
    {
        $this->history = $history;
        return $this;
    }
}
