<?php
namespace  App\Entity\Map;
use App\Entity\CommonTrait\CrupdatetimeTrait;
use App\Entity\Map\Model\MapOneToOneInterface;
use App\Entity\Map\Trait\MapOneToOneTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * PL0558 Special published sheet name 0:1
 */
#[ORM\Table(name: 'map_specialmapsheet')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class Mapspecialmapsheet implements MapOneToOneInterface
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
     * Map special sheet name. FD 558
     */
    #[ORM\Column(name: 'map_special_sheet_name', type: 'string', length: 50, nullable: false)]
    private ?string $name = null;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): Mapspecialmapsheet
    {
        $this->name = $name;
        return $this;
    }

}

