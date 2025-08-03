<?php
namespace  App\Map\Domain\Entity\Map;
use App\Area\Domain\Entity\Area;
use App\Geonames\Domain\Entity\{Country};
use App\Geonames\Domain\Entity\Admin1;
use App\Infrastructure\Doctrine\Trait\CrupdatetimeTrait;
use App\Infrastructure\Doctrine\Trait\SequenceTrait;
use App\Map\Domain\Entity\Map\Model\MapManyToOneInterface;
use App\Map\Domain\Entity\Map\Trait\MapManyToOneTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * PL0368 Further political coverage
 */
#[ORM\Table(name: 'map_furtherpc')]
#[ORM\Index(columns: ['map'], name: 'map_idx')]
#[ORM\Index(columns: ['country'], name: 'country_idx')]
#[ORM\Index(columns: ['map_scope_other_state'], name: 'admin1_idx')]
#[ORM\Index(columns: ['map_scope_other_area'], name: 'area_idx')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class Mapfurtherpc implements MapManyToOneInterface

{
    use SequenceTrait, CrupdatetimeTrait, MapManyToOneTrait;

    /**
      * FD 195
      */
     #[ORM\ManyToOne(targetEntity:  Map::class, inversedBy: 'mapfurtherpc')]
     #[ORM\JoinColumn(name: 'map', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
     private $map;

    /**
     * Map scope - other country. FD 368
     */
    #[ORM\ManyToOne(targetEntity: Country::class)]
    #[ORM\JoinColumn(name: 'country', referencedColumnName: 'id', nullable: false)]
    #[Assert\NotNull]
    private ?Country $country = null;

    /**
     *  State Admin1code. Map scope - other state. FD 369
     */
    #[ORM\ManyToOne(targetEntity: Admin1::class)]
    #[ORM\JoinColumn(name: 'map_scope_other_state', referencedColumnName: 'id', nullable: true)]
    private ?Admin1 $admin1 = null;

    /**
     * Previous area . FD 409
     */
    #[ORM\ManyToOne(targetEntity:  Area::class)]
    #[ORM\JoinColumn(name: 'map_scope_other_area', referencedColumnName: 'id', nullable: true)]
    private ?Area $area = null;

    public function getCountry(): ?Country
    {
        return $this->country;
    }

    public function setCountry(Country $country): Mapfurtherpc
    {
        $this->country = $country;
        return $this;
    }

    public function getAdmin1(): ?Admin1
    {
        return $this->admin1;
    }

    public function setAdmin1(?Admin1 $admin1): Mapfurtherpc
    {
        $this->admin1 = $admin1;
        return $this;
    }

    public function getArea(): ?Area
    {
        return $this->area;
    }

    public function setArea(?Area $area): Mapfurtherpc
    {
        $this->area = $area;
        return $this;
    }
}