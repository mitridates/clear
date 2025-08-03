<?php

namespace  App\Cave\Domain\Entity;
use App\Cave\Domain\Entity\Trait\CaveManyToOneTrait;
use App\Infrastructure\Doctrine\Trait\CrupdatetimeTrait;
use App\Infrastructure\Doctrine\Trait\SequenceTrait;
use App\Link\Domain\Entity\Link;
use Doctrine\ORM\Mapping as ORM;

/**
 * No PL
 */
#[ORM\Table(name: 'cave_link')]
#[ORM\Index(columns: ['cave'], name: 'cave_idx')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class Cavelink
{
    use SequenceTrait, CrupdatetimeTrait, CaveManyToOneTrait;
    /**
     * FD 227
     */
    #[ORM\ManyToOne(targetEntity: Cave::class, inversedBy: 'cavelink')]
    #[ORM\JoinColumn(name: 'cave', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private Cave $cave;

    /**
     * No FD
     */
    #[ORM\ManyToOne(targetEntity: Link::class)]
    #[ORM\JoinColumn(name: 'link', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private Link $link;

    /**
     * FD N/D
     */
    #[ORM\Column(name: 'comment', type: 'string', length: 120, nullable: true)]
    private ?string $comment = null;

    public function getLink(): ?Link
    {
        return $this->link;
    }

    public function setLink(Link $link): Cavelink
    {
        $this->link = $link;
        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): Cavelink
    {
        $this->comment = $comment;
        return $this;
    }

}
