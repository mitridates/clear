<?php

namespace  App\Domain\Map\Entity\Map;
use App\Domain\Link\Entity\Link;
use App\Domain\Map\Entity\Map\Model\MapManyToOneInterface;
use App\Domain\Map\Entity\Map\Trait\MapManyToOneTrait;
use CrupdatetimeTrait;
use Doctrine\ORM\Mapping as ORM;
use SequenceTrait;

/**
 * No PL
 */
#[ORM\Table(name: 'map_link')]
#[ORM\Index(columns: ['map'], name: 'map_idx')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class Maplink implements MapManyToOneInterface
{
    use SequenceTrait, CrupdatetimeTrait, MapManyToOneTrait;
    /**
     * FD 195
     */
    #[ORM\ManyToOne(targetEntity:  Map::class, inversedBy: 'maplink')]
    #[ORM\JoinColumn(name: 'map', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private $map;

    /**
     * No FD
     */
    #[ORM\ManyToOne(targetEntity: Link::class)]
    #[ORM\JoinColumn(name: 'link', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?Link $link=null;

    /**
     * FD N/D
     */
    #[ORM\Column(name: 'comment', type: 'string', length: 120, nullable: true)]
    private ?string $comment = null;

    public function getLink(): ?Link
    {
        return $this->link;
    }

    public function setLink(Link $link): Maplink
    {
        $this->link = $link;
        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): Maplink
    {
        $this->comment = $comment;
        return $this;
    }
}
