<?php
namespace  App\Cave\Domain\Entity;
use App\Area\Domain\Entity\Area;
use App\Cave\Domain\Entity\Trait\CaveManyToOneTrait;
use App\Domain\Geonames\Entity\{App\Geonames\Domain\Entity\Country};
use App\Geonames\Domain\Entity\Admin1;
use App\Infrastructure\Doctrine\Trait\CrupdatetimeTrait;
use App\Infrastructure\Doctrine\Trait\SequenceTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * CA0074 Cross-references 0:n
 */
#[ORM\Table(name: 'cave_crossreference')]
#[ORM\Index(columns: ['cave'], name: 'cave_idx')]
#[ORM\Index(columns: ['crossreference_country'], name: 'country_idx')]
#[ORM\Index(columns: ['crossreference_state'], name: 'admin1_idx')]
#[ORM\Index(columns: ['crossreference_area'], name: 'area_idx')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class Cavecrossreference
{
    use SequenceTrait, CrupdatetimeTrait, CaveManyToOneTrait;

     #[ORM\ManyToOne(targetEntity: Cave::class, inversedBy: 'cavecrossreference')]
     #[ORM\JoinColumn(name: 'cave', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
     private Cave $cave;

    /**
     *
     * FD 235
     */
    #[ORM\ManyToOne(targetEntity: \App\Geonames\Domain\Entity\Country::class)]
    #[ORM\JoinColumn(name: 'crossreference_country', referencedColumnName: 'id', nullable: false)]
    private ?\App\Geonames\Domain\Entity\Country $country = null;

    /**
     * FD 236
     */
    #[ORM\ManyToOne(targetEntity: Admin1::class)]
    #[ORM\JoinColumn(name: 'crossreference_state', referencedColumnName: 'id', nullable: false)]
    private ?Admin1 $admin1 = null;

    /**
     * State area ID. FD 237
     * @internal substitute of Area (19), area lookup (303)
     */
    #[ORM\ManyToOne(targetEntity:  Area::class)]
    #[ORM\JoinColumn(name: 'crossreference_area', referencedColumnName: 'id', nullable: true)]
    private ?Area $area = null;

    /**
     * FD 305
     */
    #[ORM\Column(name: 'crossreference_serial', type: 'string', length: 4, nullable: true)]
    private ?string $serial = null;

    public function getCountry(): ?\App\Geonames\Domain\Entity\Country
    {
        return $this->country;
    }

    public function setCountry(?\App\Geonames\Domain\Entity\Country $country): Cavecrossreference
    {
        $this->country = $country;
        return $this;
    }

    public function getAdmin1(): ?Admin1
    {
        return $this->admin1;
    }

    public function setAdmin1(?Admin1 $admin1): Cavecrossreference
    {
        $this->admin1 = $admin1;
        return $this;
    }

    public function getArea(): ?Area
    {
        return $this->area;
    }

    public function setArea(?Area $area): Cavecrossreference
    {
        $this->area = $area;
        return $this;
    }

    public function getSerial(): ?string
    {
        return $this->serial;
    }

    public function setSerial(?string $serial): Cavecrossreference
    {
        $this->serial = $serial;
        return $this;
    }
}

