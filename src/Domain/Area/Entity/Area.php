<?php
namespace  App\Domain\Area\Entity;
use App\Domain\Geonames\Entity\{Country};
use App\Domain\Geonames\Entity\Admin1;
use App\Shared\Doctrine\Orm\Id\CavernIdGenerator;
use CrupdatetimeTrait;
use Doctrine\ORM\Mapping as ORM;
use HiddenTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Region or Area Tables defined by the national speleology association.
 */
#[ORM\Table(name: 'area')]
#[ORM\Index(columns: ['country'], name: 'country_idx')]
#[ORM\Index(columns: ['admin1'], name: 'admin1_idx')]
#[ORM\UniqueConstraint(name: 'UNIQ_area_designation', columns: ['area_designation'])]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class Area
{
    use CrupdatetimeTrait, HiddenTrait;

    /**
     * FD 226
     */
    #[ORM\Column(name: 'id', type: 'string', length: 17, nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: CavernIdGenerator::class)]
    private ?string $id=null;

    /**
     * FK Country (ISO-3166 2-letter country) n-1 Country. FD 224
     */
    #[ORM\ManyToOne(targetEntity: Country::class)]
    #[ORM\JoinColumn(name: 'country', referencedColumnName: 'id', nullable: true)]
    private ?Country $country = null;

    /**
     * State supplying this ref.  FD 225
     */
    #[ORM\ManyToOne(targetEntity: Admin1::class)]
    #[ORM\JoinColumn(name: 'admin1', referencedColumnName: 'id', nullable: true)]
    private ?Admin1 $admin1 = null;

    /**
     * Area code. FD 81
     */
    #[Assert\Length(max: 3)]
    #[ORM\Column(name: 'area_code', type: 'string', length: 3, nullable: true)]
    private ?string $code = null;

    /**
     * Area name. FD 80
     */
    #[Assert\Length(max: 50, maxMessage: 'cave.validator.max.length')]
    #[ORM\Column(name: 'area_name', type: 'string', length: 50, nullable: true)]
    private ?string $name = null;

    /**
     *Area designation. FD 223 The combination of country, state, and area codes which
     *  uniquely identifies a cave/karst area or region in a national or international context.
     */
    #[Assert\Length(max: 12, maxMessage: 'cave.validator.max.length')]
    #[ORM\Column(name: 'area_designation', type: 'string', length: 12, unique: true, nullable: true)]
    private ?string $designation = null;
    /**
     * Fielddefinition 618 area_250k_map_sheet_name is defined for scale 250k BUT:
     *  - Scale 1:250k is common in Australia (516 maps) but not in other countries (Spain ING: ...1:200,000, 1:500,000...)
     *  - The list http://kid.caves.org.au/kid/help/listareas make references to areas with other scales (Armidale 1:1000000 )
     *  So 618 references to a map witch can use other scale (1:25,000, 1:50,000, 1:100,000...)
     * Map sheet. FD 618
     */
    #[Assert\Length(max: 30, maxMessage: 'cave.validator.max.length')]
    #[ORM\Column(name: 'mapsheet', type: 'string', length: 30, nullable: true)]
    private ?string $mapsheet = null;

    /**
     * Area comment. FD 621
     */
    #[Assert\Length(max: 255, maxMessage: 'cave.validator.max.length')]
    #[ORM\Column(name: 'area_comment', type: 'string', length: 255, nullable: true)]
    private ?string $comment = null;

    /**
     * use form or auto???
     */
    #[ORM\PreUpdate]
    #[ORM\PrePersist]
    public function setDesignationPrepersist():void
    {
        if ($this->admin1 !== null && $this->code !== null) {
            $this->designation = $this->admin1->getId() . '.' . $this->code;
        }
    }
 
    public function getId(): ?string
    {
        return $this->id;
    }

    public function getCountry(): ?Country
    {
        return $this->country;
    }

    public function setCountry(?Country $country): Area
    {
        $this->country = $country;
        return $this;
    }

    public function getAdmin1(): ?Admin1
    {
        return $this->admin1;
    }

    public function setAdmin1(?Admin1 $admin1): Area
    {
        $this->admin1 = $admin1;
        return $this;
    }
 
    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): Area
    {
        $this->code = $code;
        return $this;
    }
 
    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): Area
    {
        $this->name = $name;
        return $this;
    }
 
    public function getDesignation(): ?string
    {
        return $this->designation;
    }
 
    public function setDesignation(?string $designation): Area
    {
        $this->designation = $designation;
        return $this;
    }
 
    public function getMapsheet(): ?string
    {
        return $this->mapsheet;
    }
 
    public function setMapsheet(?string $mapsheet): Area
    {
        $this->mapsheet = $mapsheet;
        return $this;
    }
 
    public function getComment(): ?string
    {
        return $this->comment;
    }
 
    public function setComment(?string $comment): Area
    {
        $this->comment = $comment;
        return $this;
    }
}