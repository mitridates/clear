<?php
namespace  App\Domain\Map\Entity\Map;
use App\Domain\Map\Entity\Map\Model\MapOneToOneInterface;
use App\Domain\Map\Entity\Map\Trait\MapOneToOneTrait;
use  App\Infrastructure\Doctrine\Trait\CrupdatetimeTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * PL0579 Map comment 0:1
 */
#[ORM\Table(name: 'map_comment')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class Mapcomment implements MapOneToOneInterface
{
    use MapOneToOneTrait, CrupdatetimeTrait;

    /**
     * FD 195
     */
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    #[ORM\OneToOne(targetEntity: Map::class)]
    #[ORM\JoinColumn(name: 'map', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private Map $map;


    /**
     * FD 579
     */
    #[ORM\Column(name: 'map_comment', type: 'text', nullable: false)]
    private ?string $comment = null;

    public function setComment(string $comment): Mapcomment
    {
        $this->comment = $comment;
        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }
}

