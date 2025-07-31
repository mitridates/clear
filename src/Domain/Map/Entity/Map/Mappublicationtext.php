<?php
namespace  App\Domain\Map\Entity\Map;
use App\Domain\Map\Entity\Map\Model\MapOneToOneInterface;
use App\Domain\Map\Entity\Map\Trait\MapOneToOneTrait;
use App\Entity\CommonTrait\CrupdatetimeTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * PL0219 Map publication no id 0:1.
 * The publication details, in the case where the map has been published but no References table and Article ID is available to link to
 */
#[ORM\Table(name: 'map_publicationtext')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class Mappublicationtext implements MapOneToOneInterface
{
    use MapOneToOneTrait, CrupdatetimeTrait;

    /**
     * FD 195
     */
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    #[ORM\OneToOne(targetEntity: Map::class)]
    #[ORM\JoinColumn(name: 'map', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?Map $map;

    /**
     * Map publication text. FD 219
     */
    #[ORM\Column(name: 'map_publication_no_ID', type: 'string', length: 70, nullable: false)]
    private string $publicationtext;

    public function setPublicationtext(string $publicationtext): Mappublicationtext
    {
        $this->publicationtext = $publicationtext;
        return $this;
    }

    public function getPublicationtext(): string
    {
        return $this->publicationtext;
    }
}

