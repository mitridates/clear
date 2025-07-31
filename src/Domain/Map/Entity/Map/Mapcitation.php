<?php
namespace  App\Domain\Map\Entity\Map;
use App\Domain\Map\Entity\Map\Model\MapManyToOneInterface;
use App\Domain\Map\Entity\Map\Trait\MapManyToOneTrait;
use App\Entity\Citation\Citation;
use App\Entity\CommonTrait\{CrupdatetimeTrait};
use App\Entity\CommonTrait\SequenceTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * PL0598 Map publication (citation)
 * Unify PL0598 y PL0219 en el map_citation_comment (600:map_citation_comment y 219:map_publication_no_ID).
 */
#[ORM\Table(name: 'map_citation')]
#[ORM\Index(columns: ['map'], name: 'map_idx')]
#[ORM\Index(columns: ['map_citation_citation_id'], name: 'map_citation_citation_idx')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class Mapcitation implements MapManyToOneInterface
{
    use SequenceTrait, CrupdatetimeTrait, MapManyToOneTrait;

    /**
      * FD 195
      */
     #[ORM\ManyToOne(targetEntity:  Map::class, inversedBy: 'mapcitation')]
     #[ORM\JoinColumn(name: 'map', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
     private Map $map;

    /**
     * FD 270
     */
    #[Assert\NotBlank]
    #[ORM\ManyToOne(targetEntity:  Citation::class)]
    #[ORM\JoinColumn(name: 'map_citation_citation_id', referencedColumnName: 'id', nullable: false)]
    private ?Citation $citation=null;

    /**
     * Map citation page number. FD 599
     */
    #[ORM\Column(name: 'map_citation_page_number', type: 'string', length: 15, nullable: true)]
    private ?string $page = null;

    /**
     * Map comment. FD 600
     */
    #[ORM\Column(name: 'map_citation_comment', type: 'string', length: 70, nullable: true)]
    private ?string $comment = null;

    public function getCitation(): ?Citation
    {
        return $this->citation;
    }

    public function setCitation(Citation $citation): Mapcitation
    {
        $this->citation = $citation;
        return $this;
    }

    public function getPage(): ?string
    {
        return $this->page;
    }

    public function setPage(?string $page): Mapcitation
    {
        $this->page = $page;
        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): Mapcitation
    {
        $this->comment = $comment;
        return $this;
    }
}
