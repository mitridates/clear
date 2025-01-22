<?php
namespace  App\Entity\CommonTrait;
use Doctrine\ORM\Mapping as ORM;

trait SequenceTrait
{
    /**
     * FD 73 Value sequence
     */
    #[ORM\Column(name: 'sequence', type: 'integer', nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private ?int $sequence=null;

    /**
     * FD 73 Position in list.
     */
    #[ORM\Column(name: 'position', type: 'smallint', nullable: true, options: ['unsigned' => true])]
    private ?int $position;

    public function getSequence(): ?int
    {
        return $this->sequence;
    }

    public function setSequence(int $sequence): self
    {
        $this->sequence = $sequence;
        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): self
    {
        $this->position = $position;
        return $this;
    }


}