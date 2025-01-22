<?php
namespace  App\Entity\Cave;
use App\Entity\Cave\Trait\CaveManyToOneTrait;
use App\Entity\CommonTrait\{CrupdatetimeTrait};
use App\Entity\CommonTrait\SequenceTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * CA0053 Cave comment 0:n
 */
#[ORM\Table(name: 'cave_comment')]
#[ORM\Index(columns: ['cave'], name: 'cave_idx')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class Cavecomment
{
    use SequenceTrait, CrupdatetimeTrait, CaveManyToOneTrait;

    /**
      * FD 227
      */
     #[ORM\ManyToOne(targetEntity: Cave::class, inversedBy: 'cavecomment')]
     #[ORM\JoinColumn(name: 'cave', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
     private Cave $cave;

    /**
     * FD 53
     */
    #[ORM\Column(name: 'cave_comment', type: 'string', length: 65, nullable: true)]
    private ?string $comment = null;

    public function getComment(): string
    {
        return $this->comment;
    }

    public function setComment(string $comment): Cavecomment
    {
        $this->comment = $comment;
        return $this;
    }
}

