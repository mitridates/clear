<?php
namespace  App\Entity\Cave;
use App\Domain\Area\Entity\Area;
use App\Domain\Fielddefinition\Entity\Fieldvaluecode;
use App\Domain\Geonames\Entity\{Country};
use App\Domain\Geonames\Entity\Admin1;
use App\Domain\Geonames\Entity\Admin2;
use App\Domain\Geonames\Entity\Admin3;
use App\Entity\Cave\Trait\{CaveManyToOneRelationshipTrait, CavePartialManagementTrait};
use App\Entity\Cave\Trait\CaveOneToOneRelationshipTrait;
use App\Entity\Cave\Trait\CavePartialCoarseTrait;
use App\Entity\Cave\Trait\CavePartialDimensionTrait;
use App\Entity\Cave\Trait\CavePartialEnvironmentTrait;
use App\Entity\CommonTrait\{CrupdatetimeTrait};
use App\Entity\CommonTrait\HiddenTrait;
use App\Shared\Doctrine\Orm\Id\CavernIdGenerator;
use App\Shared\reflection\EntityReflectionHelper;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

;

/**
 * CA0000 Cave master table ()
 */
#[ORM\Table(name: 'cave')]
#[ORM\Index(columns: ['country'], name: 'country_idx')]
#[ORM\Index(columns: ['admin1'], name: 'admin1_idx')]
#[ORM\Index(columns: ['admin2'], name: 'admin2_idx')]
#[ORM\Index(columns: ['admin3'], name: 'admin3_idx')]
#[ORM\Index(columns: ['area_lookup'], name: 'area_idx')]
#[ORM\Index(columns: ['degree_explored'], name: 'fieldvaluecode_degree_explored_idx')]
#[ORM\Index(columns: ['karst_feature_type'], name: 'fieldvaluecode_feature_type_idx')]
#[ORM\Index(columns: ['entrance_type'], name: 'fieldvaluecode_entrance_type_idx')]
#[ORM\Index(columns: ['penetrability'], name: 'fieldvaluecode_penetrability_idx')]
#[ORM\Index(columns: ['entrance_marking'], name: 'fieldvaluecode_entrance_marking_idx')]
#[ORM\Index(columns: ['update_status'], name: 'fieldvaluecode_update_status_idx')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class Cave
{
    use HiddenTrait, CrupdatetimeTrait,
        CaveManyToOneRelationshipTrait,
        CaveOneToOneRelationshipTrait,
        CavePartialDimensionTrait,
        CavePartialManagementTrait,
        CavePartialEnvironmentTrait,
        CavePartialCoarseTrait
        ;

    /**
     * FD 227. Cave ID
     */
    #[ORM\Column(name: 'id', type: 'string', length: 17, nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: CavernIdGenerator::class)]
    private string $id;

    /**
     * FD 70. CA0070  Cave principal name
     */
    #[Assert\Length(max: 52)]
    #[ORM\Column(name: 'principal_name', type: 'string', length: 52, nullable: true)]
    private ?string $name = null;

    /**
     * FD Uncoded. Define if is FD 1 Karst feature type (false) || FD 8 Cave type (true). Default: null
     */
    #[ORM\Column(name: 'is_cave_type', type: 'boolean', nullable: true, options: ['default' => null])]
    private bool $iscave = true;

    /**
     * FD 1
     */
    #[ORM\ManyToOne(targetEntity: Fieldvaluecode::class)]
    #[ORM\JoinColumn(name: 'karst_feature_type', referencedColumnName: 'id')]
    private ?Fieldvaluecode $featuretype = null;

    /**
     * FD 9
     */
    #[ORM\Column(name: 'quantity_of_entrances', type: 'smallint', length: 3, nullable: true, options: ['unsigned' => true])]
    private ?int $quantityofentrances = null;

    /**
     * FD 10
     */
    #[ORM\ManyToOne(targetEntity: Fieldvaluecode::class)]
    #[ORM\JoinColumn(name: 'entrance_type', referencedColumnName: 'id')]
    private ?Fieldvaluecode $entrancetype = null;

    /**
     * FD 20
     */
    #[ORM\ManyToOne(targetEntity: Fieldvaluecode::class)]
    #[ORM\JoinColumn(name: 'penetrability', referencedColumnName: 'id')]
    private ?Fieldvaluecode $penetrability = null;

    /**
     * FD 78
     */
    #[ORM\ManyToOne(targetEntity: Fieldvaluecode::class)]
    #[ORM\JoinColumn(name: 'entrance_marking', referencedColumnName: 'id')]
    private ?Fieldvaluecode $entrancemarking = null;

    /**
     * FD 512
     */
    #[ORM\ManyToOne(targetEntity: Fieldvaluecode::class)]
    #[ORM\JoinColumn(name: 'update_status', referencedColumnName: 'id')]
    private ?Fieldvaluecode $updatestatus = null;

    /**
     * FD 29
     */
    #[ORM\ManyToOne(targetEntity: Fieldvaluecode::class)]
    #[ORM\JoinColumn(name: 'degree_explored', referencedColumnName: 'id')]
    private ?Fieldvaluecode $degreeexplored = null;

    /**
     * FD 54
     */
    #[ORM\Column(name: 'percent_mapped', type: 'smallint', length: 4, nullable: true, options: ['unsigned' => true])]
    private ?int $percentmapped = null;

    /**
     * FD 229
     */
    #[ORM\Column(name: 'local_government_area', type: 'string', length: 30, nullable: true)]
    private ?string $localgovernmentarea = null;

    /**
     * FD 27
     */
    #[ORM\Column(name: 'nearest_locality', type: 'string', length: 30, nullable: true)]
    private ?string $nearestlocality = null;

    /**
     * FD 220
     */
    #[ORM\ManyToOne(targetEntity: Country::class)]
    #[ORM\JoinColumn(name: 'country', referencedColumnName: 'id', nullable: true)]
    private ?Country $country = null;

    /**
     * State area ID. FD 18
     */
    #[ORM\ManyToOne(targetEntity: Admin1::class)]
    #[ORM\JoinColumn(name: 'admin1', referencedColumnName: 'id', nullable: true)]
    private ?Admin1 $admin1 = null;

    /**
     * No FD
     */
    #[ORM\ManyToOne(targetEntity: Admin2::class)]
    #[ORM\JoinColumn(name: 'admin2', referencedColumnName: 'id', nullable: true)]
    private ?Admin2 $admin2 = null;

    /**
     * No FD
     */
    #[ORM\ManyToOne(targetEntity: Admin3::class)]
    #[ORM\JoinColumn(name: 'admin3', referencedColumnName: 'id', nullable: true)]
    private ?Admin3 $admin3 = null;

    /**
     * FD  303. Area lookup.
     */
    #[ORM\ManyToOne(targetEntity:  Area::class)]
    #[ORM\JoinColumn(name: 'area_lookup', referencedColumnName: 'id', nullable: true)]
    private ?Area $area = null;

    /**
     * FD  19. Area in cave number. Use area lookup
     */
    #[ORM\Column(name: 'area_in_cave_number', type: 'string', length: 17, nullable: true)]
    private ?string $areaincavenumber = null;
    /**
     * Geographical feature. FD Local code 13075
     */
    #[ORM\Column(name: 'geographical_location', type: 'string', length: 30, nullable: true)]
    private ?string $geographiclocation = null;

    /**
     * FD Local code 13076. Cave system if any
     */
    #[ORM\Column(name: 'cave_system', type: 'string', length: 30, nullable: true)]
    private ?string $system = null;

    /**
     * FD 77
     */
    #[ORM\Column(name: 'serial_in_cave_number', type: 'string', length: 4, nullable: true)]
    private ?string $serial = null;

    /**
     * FD 531
     */
    #[ORM\Column(name: 'orientation', type: 'decimal', precision: 5, scale: 1, nullable: true)]
    private ?float $orientation = null;

    public function getId(): ?string
    {
        return $this->id;
    }
 
    public function setName(string $name): Cave
    {
        $this->name = $name;
        return $this;
    }
 
    public function getName(): ?string
    {
        return $this->name;
    }

    public function getIscave(): bool
    {
        return $this->iscave;
    }

    public function setIscave(bool $iscave): Cave
    {
        $this->iscave = $iscave;
        return $this;
    }
    
    public function setPenetrability(?Fieldvaluecode $penetrability): Cave
    {
        $this->penetrability = $penetrability;
        return $this;
    }

    public function getPenetrability(): ?Fieldvaluecode
    {
        return $this->penetrability;
    }

    public function setFeaturetype(?Fieldvaluecode $featuretype): Cave
    {
        $this->featuretype = $featuretype;
        return $this;
    }

    public function getFeaturetype(): ?Fieldvaluecode
    {
        return $this->featuretype;
    }

    public function setQuantityofentrances(?int $quantityofentrances): Cave
    {
        $this->quantityofentrances = $quantityofentrances;
        return $this;
    }

    public function getQuantityofentrances(): ?int
    {
        return $this->quantityofentrances;
    }

    public function setEntrancetype(?Fieldvaluecode $entrancetype): Cave
    {
        $this->entrancetype = $entrancetype;
        return $this;
    }

    public function getEntrancetype(): ?Fieldvaluecode
    {
        return $this->entrancetype;
    }

    public function setEntrancemarking(?Fieldvaluecode $entrancemarking): Cave
    {
        $this->entrancemarking = $entrancemarking;
        return $this;
    }

    public function getEntrancemarking(): ?Fieldvaluecode
    {
        return $this->entrancemarking;
    }

    public function setUpdatestatus(?Fieldvaluecode $updatestatus): Cave
    {
        $this->updatestatus = $updatestatus;
        return $this;
    }

    public function getUpdatestatus(): ?Fieldvaluecode
    {
        return $this->updatestatus;
    }

    public function getDegreeexplored(): ?Fieldvaluecode
    {
        return $this->degreeexplored;
    }

    public function setDegreeexplored(?Fieldvaluecode $degreeexplored): Cave
    {
        $this->degreeexplored = $degreeexplored;
        return $this;
    }

    public function getPercentmapped(): ?int
    {
        return $this->percentmapped;
    }

    public function setPercentmapped(?int $percentmapped): Cave
    {
        $this->percentmapped = $percentmapped;
        return $this;
    }

    public function setLocalgovernmentarea(?string $localgovernmentarea): Cave
    {
        $this->localgovernmentarea = $localgovernmentarea;
        return $this;
    }

    public function getLocalgovernmentarea(): ?string
    {
        return $this->localgovernmentarea;
    }

    public function setNearestlocality(?string $nearestlocality): Cave
    {
        $this->nearestlocality = $nearestlocality;
        return $this;
    }

    public function getNearestlocality(): ?string
    {
        return $this->nearestlocality;
    }

    public function setCountry(?Country $country): Cave
    {
        $this->country = $country;
        return $this;
    }

    public function getCountry(): ?Country
    {
        return $this->country;
    }

    public function setAdmin1(?Admin1 $admin1): Cave
    {
        $this->admin1 = $admin1;
        return $this;
    }

    public function getAdmin1(): ?Admin1
    {
        return $this->admin1;
    }

    public function setAdmin2(?Admin2 $admin2): Cave
    {
        $this->admin2 = $admin2;
        return $this;
    }

    public function getAdmin2(): ?Admin2
    {
        return $this->admin2;
    }

    public function setAdmin3(?Admin3 $admin3): Cave
    {
        $this->admin3 = $admin3;
        return $this;
    }

    public function getAdmin3(): ?Admin3
    {
        return $this->admin3;
    }

    public function setArea(?Area $area): Cave
    {
        $this->area = $area;
        return $this;
    }

    public function getArea(): ?Area
    {
        return $this->area;
    }    

    function setGeographiclocation(?string $geographiclocation): Cave
    {
        $this->geographiclocation = $geographiclocation;
        return $this;
    }

    function getGeographiclocation(): ?string
    {
        return $this->geographiclocation;
    }

    function setAreaincavenumber(?string $areaincavenumber): Cave
    {
        $this->areaincavenumber = $areaincavenumber;
        return $this;
    }

    function getAreaincavenumber(): ?string
    {
        return $this->areaincavenumber;
    }

    function setSystem(?string $system): Cave
    {
        $this->system = $system;
        return $this;
    }

    function getSystem(): ?string
    {
        return $this->system;
    }

    public function setSerial(?string $serial): Cave
    {
        $this->serial = $serial;
        return $this;
    }

    public function getOrientation(): ?string
    {
        return $this->orientation;
    }
 
    public function setOrientation(?float $orientation): Cave
    {
        $this->orientation = $orientation;
        return $this;
    }

    public function getSerial(): ?string
    {
        return $this->serial;
    }

    public function hasDimensions():bool
    {
        return !EntityReflectionHelper::isEmptyTrait($this, CavePartialDimensionTrait::class);
    }
    public function hasManagement():bool
    {
        return !EntityReflectionHelper::isEmptyTrait($this, CavePartialManagementTrait::class);
    }
    public function hasEnvironment():bool
    {
        return !EntityReflectionHelper::isEmptyTrait($this, CavePartialEnvironmentTrait::class);
    }
    public function hasCoarse():bool
    {
        return !EntityReflectionHelper::isEmptyTrait($this, CavePartialCoarseTrait::class);
    }

}