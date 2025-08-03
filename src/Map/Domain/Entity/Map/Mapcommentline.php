<?php
namespace  App\Map\Domain\Entity\Map;
use App\Infrastructure\Doctrine\Trait\CrupdatetimeTrait;
use App\Infrastructure\Doctrine\Trait\SequenceTrait;
use App\Map\Domain\Entity\Map\Model\MapOneToOneInterface;
use App\Map\Domain\Entity\Map\Trait\MapManyToOneTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * PL0579  Map comment line
 */
#[ORM\Table(name: 'map_commentline')]
#[ORM\Index(columns: ['map'], name: 'map_idx')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class Mapcommentline implements mapOneToOneInterface
{
    use SequenceTrait, CrupdatetimeTrait, MapManyToOneTrait;

    /**
     * FD 195
     */
    #[ORM\ManyToOne(targetEntity:  Map::class, inversedBy: 'mapcommentline')]
    #[ORM\JoinColumn(name: 'map', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private $map;

    /**
     * Map comment memo. FD 218
     */
    #[Assert\NotBlank]
    #[ORM\Column(name: 'map_comment_line', type: 'string', length: 70, nullable: false)]
    private ?string $comment = null;

    public function getComment(): string
    {
        return $this->comment;
    }

    public function setComment(string $comment): Mapcommentline
    {
        $this->comment = $comment;
        return $this;
    }
}
