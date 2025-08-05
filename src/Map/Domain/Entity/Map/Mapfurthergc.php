<?php
namespace  App\Map\Domain\Entity\Map;
use App\Map\Domain\Entity\Map\Model\MapManyToOneInterface;
use App\Map\Domain\Entity\Map\Trait\MapManyToOneTrait;
use App\Shared\Doctrine\Trait\CrupdatetimeTrait;
use App\Shared\Doctrine\Trait\SequenceTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * Further geographic coverage (PL0397)
 */
#[ORM\Table(name: 'map_furthergc')]
#[ORM\Index(columns: ['map'], name: 'map_idx')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class Mapfurthergc implements MapManyToOneInterface
{
    use SequenceTrait, CrupdatetimeTrait, MapManyToOneTrait;

    /**
      * FD 195
      */
     #[ORM\ManyToOne(targetEntity:  Map::class, inversedBy: 'mapfurthergc')]
     #[ORM\JoinColumn(name: 'map', referencedColumnName: 'id', nullable: true, onDelete: 'CASCADE')]
     private $map;

    /**
     * Map scope - N latitude. FD 397
     */
    #[ORM\Column(name: 'map_scope_other_N_lat', type: 'decimal', precision: 11, scale: 7, nullable: true)]
    private ?float $northlatitude = null;

    /**
     * Map scope - S latitude. FD 398
     */
    #[ORM\Column(name: 'map_scope_other_S_lat', type: 'decimal', precision: 11, scale: 7, nullable: true)]
    private ?float $southlatitude = null;

    /**
     * Map scope - E longitude. FD 399
     */
    #[ORM\Column(name: 'map_scope_other_E_long', type: 'decimal', precision: 11, scale: 7, nullable: true)]
    private ?float $eastlongitude = null;

    /**
     * Map scope - W longitude. FD 400
     */
    #[ORM\Column(name: 'map_scope_other_W_long', type: 'decimal', precision: 11, scale: 7, nullable: true)]
    private ?float $westlongitude = null;

    public function setNorthlatitude(?float $northlatitude): Mapfurthergc
    {
        $this->northlatitude = $northlatitude;
        return $this;
    }

    /**
     * Decimal fields cannot be type-hinted as float
     * @link https://github.com/symfony/symfony/issues/32124
     * @return ?string
     */
    public function getNorthlatitude(): ?string
    {
        return $this->northlatitude;
    }

    public function setSouthlatitude(?float $southlatitude): Mapfurthergc
    {
        $this->southlatitude = $southlatitude;
        return $this;
    }

    /**
     * Decimal fields cannot be type-hinted as float
     * @link https://github.com/symfony/symfony/issues/32124
     */
    public function getSouthlatitude(): ?string
    {
        return $this->southlatitude;
    }

    public function setEastlongitude(?float $eastlongitude): Mapfurthergc
    {
        $this->eastlongitude = $eastlongitude;
        return $this;
    }

    /**
     * Decimal fields cannot be type-hinted as float
     * @link https://github.com/symfony/symfony/issues/32124
     * @return ?string
     */
    public function getEastlongitude(): ?string
    {
        return $this->eastlongitude;
    }

    public function setWestlongitude(?float $westlongitude): Mapfurthergc
    {
        $this->westlongitude = $westlongitude;
        return $this;
    }

    /**
     * Decimal fields cannot be type-hinted as float
     * @link https://github.com/symfony/symfony/issues/32124
     */
    public function getWestlongitude(): ?string
    {
        return $this->westlongitude;
    }
//    #[Assert\Callback]
//    public function validate(ExecutionContextInterface $context, mixed $payload): void
//    {
//        if(
//            empty($this->getEastlongitude()) &&
//            empty($this->getNorthlatitude()) &&
//            empty($this->getSouthlatitude()) &&
//            empty($this->getWestlongitude())
//        ){
//            $context->buildViolation('form.emptynotallow')
//                ->addViolation();
//        }
//    }
}
