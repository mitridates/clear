<?php
namespace  App\SystemParameter\Domain\Entity;
use App\Fielddefinition\Domain\Entity\Fieldvaluecode;
use App\Geonames\Domain\Entity\Country;
use App\Infrastructure\Doctrine\Trait\CrupdatetimeTrait;
use App\Organisation\Domain\Entity\Organisation;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * System parameters
 */
#[ORM\Table(name: 'system_parameters')]
#[ORM\Index(columns: ['country'], name: 'country_idx')]
#[ORM\Index(columns: ['organisation_site'], name: 'organisation_site_idx')]
#[ORM\Index(columns: ['organisation_dbm'], name: 'organisation_dbm_idx')]
#[ORM\Index(columns: ['grid_ref_units'], name: 'fieldvaluecode_grid_ref_units_idx')]
#[ORM\Index(columns: ['altitude_unit'], name: 'fieldvaluecode_altitude_units_idx')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class SystemParameter
{

    use  CrupdatetimeTrait;

    #[ORM\Column(name: 'id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private int $id;

    /**
     * No FD 
     */
    #[ORM\Column(name: 'name', type: 'string', length: 30, nullable: false)]
    #[Assert\NotNull]
    private ?string $name = null;

    /**
     * No FD
     */
    #[ORM\Column(name: 'active', type: 'boolean', nullable: false, options: ['default' => 0])]
    private ?bool $active = null;

    /**
     * Site country code. FD 156
     */
    #[ORM\ManyToOne(targetEntity: Country::class)]
    #[ORM\JoinColumn(name: 'country', referencedColumnName: 'id', nullable: true)]
    private ?Country $country=null;

    /**
     * Site organisation code. The organisation operating this database site. FD 157
     */
    #[ORM\ManyToOne(targetEntity: Organisation::class)]
    #[ORM\JoinColumn(name: 'organisation_site', referencedColumnName: 'id', nullable: true)]
    private ?Organisation $organisationsite = null;

    /**
     * Database org code. The organisation controlling
     *  the standards and design for the database being used at this site.
     *  Is the ID generator organisation. FD 182
     */
    #[ORM\ManyToOne(targetEntity: Organisation::class)]
    #[ORM\JoinColumn(name: 'organisation_dbm', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    #[Assert\NotNull]
    private ?Organisation $organisationdbm = null;

    /**
     * Site default locale. ISO 2-letter language code, normally in lower case. FD 158
     */
    #[Assert\Language]
    #[ORM\Column(name: 'language', type: 'string', length: 2, nullable: true)]
    private ?string $language = null;

    /**
     * FD 288
     */
    #[ORM\Column(name: 'geographic_geodetic_datum', type: 'string', length: 20, nullable: true)]
    private ?string $geodeticdatum = null;

    /**
     * Map grid. FD 289
     */
    #[ORM\Column(name: 'grid_ref_map_grid', type: 'string', length: 3, nullable: true)]
    private ?string $mapgrid = null;

    /**
     * FD 516
     */
    #[ORM\Column(name: 'map_height_datum', type: 'string', length: 2, nullable: true)]
    private ?string $heightdatum = null;

    /**
     * FD 412
     */
    #[ORM\Column(name: 'mapserie', type: 'string', length: 30, nullable: true)]
    private ?string $mapserie = null;

    /**
     * FD 296
     */
    #[ORM\ManyToOne(targetEntity: Fieldvaluecode::class)]
    #[ORM\JoinColumn(name: 'grid_ref_units', referencedColumnName: 'id')]
    private ?Fieldvaluecode $refunits = null;

    /**
     * FD 413
     */
    #[ORM\Column(name: 'grref_qualifier', type: 'string', length: 4, nullable: true)]
    private ?string $grrefqualifier = null;

    /**
     * FD 294
     */
    #[ORM\Column(name: 'position_geog_precision', type: 'decimal', precision: 8, scale: 5, nullable: true)]
    private ?float $geogprecision = null;

    /**
     * FD 295
     */
    #[ORM\Column(name: 'position_gref_precision', type: 'decimal', precision: 10, scale: 3, nullable: true)]
    private ?float $grefprecision = null;

    /**
     * FD 526
     */
    #[ORM\Column(name: 'altitude_precision', type: 'decimal', precision: 8, scale: 3, nullable: true)]
    private ?float $altitudeprecision = null;

    /**
     * FD 440.
     */
    #[ORM\Column(name: 'land_unit', type: 'string', length: 30, nullable: true)]
    private ?string $landunit = null;

    /**
     * FD 446
     */
    #[ORM\ManyToOne(targetEntity: Fieldvaluecode::class)]
    #[ORM\JoinColumn(name: 'altitude_unit', referencedColumnName: 'id')]
    private ?Fieldvaluecode $altitudeunit = null;

    /**
     * @var ?bool FD 291 Code translations Y/N
     */
    #[ORM\Column(name: 'trans_codes', type: 'boolean', nullable: true, options: ['fixed' => true])]
    private ?bool $transcodes = null;

    /**
     * FD 529 Software upgrade level
     */
    #[ORM\Column(name: 'software_level', type: 'string', length: 4, nullable: true)]
    private ?string $softwarelevel = null;

    /**
     * FD 539 Software version number
     */
    #[ORM\Column(name: 'software_version', type: 'string', length: 15, nullable: true)]
    private ?string $version = null;

    /**
     * FD 596 Map images directory path
     */
    #[ORM\Column(name: 'map_dir', type: 'string', length: 50, nullable: true)]
    private ?string $mapdir = null;

    /**
     * Map images directory path. FD 13079 internal
     */
    #[ORM\Column(name: 'topo_dir', type: 'string', length: 50, nullable: true)]
    private ?string $topodir = null;

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): SystemParameter
    {
        $this->name = $name;
        return $this;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(?bool $active): SystemParameter
    {
        $this->active = $active;
        return $this;
    }

    public function getCountry(): ?Country
    {
        return $this->country;
    }

    public function setCountry(?Country $country): SystemParameter
    {
        $this->country = $country;
        return $this;
    }

    public function getOrganisationsite(): ?Organisation
    {
        return $this->organisationsite;
    }

    public function setOrganisationsite(?Organisation $organisationsite): SystemParameter
    {
        $this->organisationsite = $organisationsite;
        return $this;
    }

    public function getOrganisationdbm(): ?Organisation
    {
        return $this->organisationdbm;
    }

    public function setOrganisationdbm(?Organisation $organisationdbm= null): SystemParameter
    {
        $this->organisationdbm = $organisationdbm;
        return $this;
    }

    public function getLanguage(): ?string
    {
        return $this->language;
    }

    public function setLanguage(?string $language): SystemParameter
    {
        $this->language = $language;
        return $this;
    }

    public function getGeodeticdatum(): ?string
    {
        return $this->geodeticdatum;
    }

    public function setGgeodeticdatum(?string $geodeticdatum): SystemParameter
    {
        $this->geodeticdatum = $geodeticdatum;
        return $this;
    }

    public function getMapgrid(): ?string
    {
        return $this->mapgrid;
    }

    public function setMapgrid(?string $mapgrid): SystemParameter
    {
        $this->mapgrid = $mapgrid;
        return $this;
    }

    public function getHeightdatum(): ?string
    {
        return $this->heightdatum;
    }

    public function setHeightdatum(?string $heightdatum): SystemParameter
    {
        $this->heightdatum = $heightdatum;
        return $this;
    }

    public function getMapserie(): ?string
    {
        return $this->mapserie;
    }

    public function setMapserie(?string $mapserie): SystemParameter
    {
        $this->mapserie = $mapserie;
        return $this;
    }

    public function getRefunits(): ?Fieldvaluecode
    {
        return $this->refunits;
    }

    public function setRefunits(?Fieldvaluecode $refunits): SystemParameter
    {
        $this->refunits = $refunits;
        return $this;
    }

    public function getGrrefqualifier(): ?string
    {
        return $this->grrefqualifier;
    }

    public function setGrrefqualifier(?string $grrefqualifier): SystemParameter
    {
        $this->grrefqualifier = $grrefqualifier;
        return $this;
    }
    /**
     * Decimal fields cannot be type-hinted as float
     * @link https://github.com/symfony/symfony/issues/32124
     */
    public function getGeogprecision(): ?string
    {
        return $this->geogprecision;
    }
    /**
     * Decimal fields cannot be type-hinted as float
     * @link https://github.com/symfony/symfony/issues/32124
     */
    public function setGeogprecision(?float $geogprecision): SystemParameter
    {
        $this->geogprecision = $geogprecision;
        return $this;
    }
    /**
     * Decimal fields cannot be type-hinted as float
     * @link https://github.com/symfony/symfony/issues/32124
     */
    public function getGrefprecision(): ?string
    {
        return $this->grefprecision;
    }

    public function setGrefprecision(?float $grefprecision): SystemParameter
    {
        $this->grefprecision = $grefprecision;
        return $this;
    }
    /**
     * Decimal fields cannot be type-hinted as float
     * @link https://github.com/symfony/symfony/issues/32124
     */
    public function getAltitudeprecision(): ?string
    {
        return $this->altitudeprecision;
    }

    public function setAltitudeprecision(?float $altitudeprecision): SystemParameter
    {
        $this->altitudeprecision = $altitudeprecision;
        return $this;
    }

    public function getLandunit(): ?string
    {
        return $this->landunit;
    }

    public function setLandunit(?string $landunit): SystemParameter
    {
        $this->landunit = $landunit;
        return $this;
    }

    public function getAltitudeunit(): ?Fieldvaluecode
    {
        return $this->altitudeunit;
    }

    public function setAltitudeunit(?Fieldvaluecode $altitudeunit): SystemParameter
    {
        $this->altitudeunit = $altitudeunit;
        return $this;
    }

    public function getTranscodes(): ?bool
    {
        return $this->transcodes;
    }

    public function setTranscodes(?bool $transcodes): SystemParameter
    {
        $this->transcodes = $transcodes;
        return $this;
    }

    public function getSoftwarelevel(): ?string
    {
        return $this->softwarelevel;
    }

    public function setSoftwarelevel(?string $softwarelevel): SystemParameter
    {
        $this->softwarelevel = $softwarelevel;
        return $this;
    }

    public function getVersion(): ?string
    {
        return $this->version;
    }

    public function setVersion(?string $version): SystemParameter
    {
        $this->version = $version;
        return $this;
    }

    public function getMapdir(): ?string
    {
        return $this->mapdir;
    }

    public function setMapdir(?string $mapdir): SystemParameter
    {
        $this->mapdir = $mapdir;
        return $this;
    }

    public function getTopodir(): ?string
    {
        return $this->topodir;
    }

    public function setTopodir(?string $topodir): SystemParameter
    {
        $this->topodir = $topodir;
        return $this;
    }

}
