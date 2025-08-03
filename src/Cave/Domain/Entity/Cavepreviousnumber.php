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
 * CA0231 Previous cave numbers 0:n
 */
#[ORM\Table(name: 'cave_previousnumber')]
#[ORM\Index(columns: ['cave'], name: 'cave_idx')]
#[ORM\Index(columns: ['previous_country'], name: 'country_idx')]
#[ORM\Index(columns: ['previous_state'], name: 'admin1_idx')]
#[ORM\Index(columns: ['previous_area'], name: 'area_idx')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class Cavepreviousnumber
{
    use SequenceTrait, CrupdatetimeTrait, CaveManyToOneTrait;

    /**
      * FD 227
      */
     #[ORM\ManyToOne(targetEntity: Cave::class, inversedBy: 'cavepreviousnumber')]
     #[ORM\JoinColumn(name: 'cave', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
     private Cave $cave;

    /**
     * FD 231
     */
    #[ORM\ManyToOne(targetEntity: \App\Geonames\Domain\Entity\Country::class)]
    #[ORM\JoinColumn(name: 'previous_country', referencedColumnName: 'id', nullable: true)]
    private ?\App\Geonames\Domain\Entity\Country $country = null;

    /**
     * Previous state. FD 232
     */
    #[ORM\ManyToOne(targetEntity: Admin1::class)]
    #[ORM\JoinColumn(name: 'previous_state', referencedColumnName: 'id', nullable: true)]
    private ?Admin1 $admin1 = null;

    /**
     * @var ?Area Previous area . FD 233
     */
    #[ORM\ManyToOne(targetEntity:  Area::class)]
    #[ORM\JoinColumn(name: 'previous_area', referencedColumnName: 'id', nullable: true)]
    private ?Area $area = null;

    /**
     * Previous serial. FD 234
     */
    #[ORM\Column(name: 'previous_serial', type: 'string', length: 4, nullable: true)]
    private ?string $serial = null;

    /**
     * Previous area lookup. FD 304
     */
    #[ORM\Column(name: 'previous_area_lookup', type: 'string', length: 7, nullable: true)]
    private ?string $arealookup = null;

    public function getCountry(): ?\App\Geonames\Domain\Entity\Country
    {
        return $this->country;
    }

    public function setCountry(?\App\Geonames\Domain\Entity\Country $country): Cavepreviousnumber
    {
        $this->country = $country;
        return $this;
    }

    public function getAdmin1(): ?Admin1
    {
        return $this->admin1;
    }

    public function setAdmin1(?Admin1 $admin1): Cavepreviousnumber
    {
        $this->admin1 = $admin1;
        return $this;
    }

    public function getArea(): ?Area
    {
        return $this->area;
    }

    public function setArea(?Area $area): Cavepreviousnumber
    {
        $this->area = $area;
        return $this;
    }

    public function getSerial(): ?string
    {
        return $this->serial;
    }

    public function setSerial(?string $serial): Cavepreviousnumber
    {
        $this->serial = $serial;
        return $this;
    }

    public function getArealookup(): ?string
    {
        return $this->arealookup;
    }

    public function setArealookup(?string $arealookup): Cavepreviousnumber
    {
        $this->arealookup = $arealookup;
        return $this;
    }
}
