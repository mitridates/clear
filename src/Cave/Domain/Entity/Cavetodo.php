<?php
namespace  App\Cave\Domain\Entity;
use App\Cave\Domain\Entity\Trait\CaveManyToOneTrait;
use App\Fielddefinition\Domain\Entity\Fieldvaluecode;
use App\Shared\Doctrine\Trait\CrupdatetimeTrait;
use App\Shared\Doctrine\Trait\SequenceTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * To do in this registry (uncoded) 0:n
 */
#[ORM\Table(name: 'cave_todo')]
#[ORM\Index(columns: ['cave'], name: 'cave_idx')]
#[ORM\Index(columns: ['priority'], name: 'priority_idx')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class Cavetodo
{
    use SequenceTrait, CrupdatetimeTrait, CaveManyToOneTrait;

    /**
      * FD 227
      */
     #[ORM\ManyToOne(targetEntity: Cave::class, inversedBy: 'cavetodo')]
     #[ORM\JoinColumn(name: 'cave', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
     protected $cave;

    /**
     * To do in table uncoded
     */
    #[ORM\Column(name: 'cave_todo', type: 'string', length: 255, nullable: false)]
    private ?string $todo = null;

    /**
     * FD * , Priority. FD local coded(10002).
     */
    #[ORM\ManyToOne(targetEntity: Fieldvaluecode::class)]
    #[ORM\JoinColumn(name: 'priority', referencedColumnName: 'id')]
    private ?Fieldvaluecode $priority = null;

    public function getTodo(): string
    {
        return $this->todo;
    }

    public function setTodo(string $todo): Cavetodo
    {
        $this->todo = $todo;
        return $this;
    }

    public function getPriority(): ?Fieldvaluecode
    {
        return $this->priority;
    }

    public function setPriority(Fieldvaluecode $priority): Cavetodo
    {
        $this->priority = $priority;
        return $this;
    }
}
