<?php
namespace  App\Entity\Map;
use App\Entity\Area;
use App\Entity\CommonTrait\CrupdatetimeTrait;
use App\Entity\CommonTrait\HiddenTrait;
use App\Entity\FieldDefinition\Fieldvaluecode;
use App\Entity\Geonames\{Admin1, Admin2, Admin3, Country};
use App\Entity\Map\Model\MapInterface;
use App\Entity\Mapserie;
use App\Entity\Organisation;
use App\Entity\Person;
use App\Utils\Doctrine\Orm\Id\CavernIdGenerator;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

;

/**
 * Map master table (PL0000)
 *
 * Fields moved to Mapdetail entity
 *  555:vectorversionavail, 556:rasterversionavail, 403:digitalmaster, 213:microfilmed
 * Fields included from Mapdetail 
 * TODO carga demasiado, pasar a mapdetail
 * 203:surveygradeorg, 204:surveygradevalue,
 * 206('10103:viewsshownp,10104:viewsshownl,10105:viewsshownx)
 * 607:surveystartyear,207:surveyfinishyear,402:principaldrafterid
 */
#[ORM\Table(name: 'map')]
#[ORM\Index(columns: ['map_host_area_map_ID'], name: 'area_host_map_idx')]
#[ORM\Index(columns: ['map_scope_area_code'], name: 'area_scope_idx')]
#[ORM\Index(columns: ['map_scope_country_code'], name: 'country_scope_idx')]
#[ORM\Index(columns: ['map_scope_state_code'], name: 'admin1_scope_idx')]
#[ORM\Index(columns: ['map_scope_admin2'], name: 'admin2_scope_idx')]
#[ORM\Index(columns: ['map_scope_admin3'], name: 'admin3_scope_idx')]
#[ORM\Index(columns: ['map_type'], name: 'fieldvaluecode_map_type_idx')]
#[ORM\Index(columns: ['map_source_country_code'], name: 'country_source_idx')]
#[ORM\Index(columns: ['map_source_org_code'], name: 'organisation_source_idx')]
#[ORM\Index(columns: ['mapserie'], name: 'mapserie_idx')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class Map implements MapInterface
{
    use HiddenTrait, CrupdatetimeTrait;//, MapManyToOneRelationshipTrait, MapOneToOneRelationshipTrait;

    /**
     * FD 195. Map ID
     */
    #[ORM\Column(name: 'id', type: 'string', length: 17, nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: CavernIdGenerator::class)]
    private string $id;

    /**
     * Map or sheet name. FD 202
     */
    #[Assert\Length(max: 30)]
    #[ORM\Column(name: 'map_or_sheet_name', type: 'string', length: 30, nullable: true)]
    private ?string $name = null;

    /**
     * Map number. FD 271
     */
    #[ORM\Column(name: 'map_number', type: 'string', length: 25, nullable: true)]
    private ?string $number = null;

    /**
     * Map subsheet name. FD 272
     */
    #[ORM\Column(name: 'map_subsheet_name', type: 'string', length: 26, nullable: true)]
    private ?string $subsheetname = null;

    /**
     * FD 366
     */
    #[ORM\ManyToOne(targetEntity:  Mapserie::class)]
    #[ORM\JoinColumn(name: 'mapserie', referencedColumnName: 'id', nullable: true)]
    private ?Mapserie $mapserie = null;

    /**
     * FD 205
     */
    #[ORM\Column(name: 'map_scale', type: 'decimal', precision: 10, scale: 1, nullable: true)]
    private ?string $scale = null;

    /**
     * FD 367
     */
    #[ORM\ManyToOne(targetEntity: Fieldvaluecode::class)]
    #[ORM\JoinColumn(name: 'map_type', referencedColumnName: 'id')]
    private ?Fieldvaluecode $type = null;

    /**
     * FD 551
     */
    #[ORM\Column(name: 'map_geodetic_datum', type: 'string', length: 20, nullable: true)]
    private ?string $geodeticdatum = null;

    /**
     * FD 552
     */
    #[ORM\Column(name: 'map_height_datum', type: 'string', length: 20, nullable: true)]
    private ?string $heightdatum = null;

    /**
     * FD 553
     */
    #[ORM\Column(name: 'map_grid', type: 'string', length: 20, nullable: true)]
    private ?string $grid = null;

    /**
     * Map source - country code. FD 370
     */
    #[ORM\ManyToOne(targetEntity: Country::class)]
    #[ORM\JoinColumn(name: 'map_source_country_code', referencedColumnName: 'id', nullable: true)]
    private ?Country $sourcecountry = null;

    /**
     * Map source - org code. FD 200
     */
    #[ORM\ManyToOne(targetEntity: Organisation::class)]
    #[ORM\JoinColumn(name: 'map_source_org_code', referencedColumnName: 'id')]
    private ?Organisation $sourceorg = null;


    /**
     * FD 396. Codes 623.
     */
    #[ORM\ManyToOne(targetEntity: Fieldvaluecode::class)]
    #[ORM\JoinColumn(name: 'map_source_type', referencedColumnName: 'id')]
    private ?Fieldvaluecode $sourcetype = null;


    /**
     * Map source if no ID. FD 209
     */
    #[ORM\Column(name: 'map_source_if_no_ID', type: 'string', length: 70, nullable: true)]
    private ?string $sourceifnoid = null;

    /**
     * Map scope - country code. FD 196
     */
    #[ORM\ManyToOne(targetEntity: Country::class)]
    #[ORM\JoinColumn(name: 'map_scope_country_code', referencedColumnName: 'id', nullable: true)]
    private ?Country $country = null;

    /**
     * Map scope - state code. FD 197
     */
    #[ORM\ManyToOne(targetEntity: Admin1::class)]
    #[ORM\JoinColumn(name: 'map_scope_state_code', referencedColumnName: 'id', nullable: true)]
    private ?Admin1 $admin1 = null;

    /**
     * Map scope - province. FD uncoded
     **/
    #[ORM\ManyToOne(targetEntity: Admin2::class)]
    #[ORM\JoinColumn(name: 'map_scope_admin2', referencedColumnName: 'id', nullable: true)]
    private ?Admin2 $admin2 = null;

    /**
     * Map scope - Municipality. FD uncoded
     **/
    #[ORM\ManyToOne(targetEntity: Admin3::class)]
    #[ORM\JoinColumn(name: 'map_scope_admin3', referencedColumnName: 'id', nullable: true)]
    private ?Admin3 $admin3 = null;

    /**
     * Map scope - area code. FD 198
     * If required, the code for the main area or region covered by the map.
     * @todo add to forms
     */
    #[ORM\ManyToOne(targetEntity:  Area::class)]
    #[ORM\JoinColumn(name: 'map_scope_area_code', referencedColumnName: 'id', nullable: true)]
    private ?Area $area = null;


    /**
     * The internal Record ID of the area map on which this cave map exists,
     * if the cave map exists only on the area map and the area
     * map cannot be identified from the cave map number
     * @todo  add to forms
     * Map host area map ID. FD 211
     */
    #[ORM\ManyToOne(targetEntity:  Area::class)]
    #[ORM\JoinColumn(name: 'map_host_area_map_ID', referencedColumnName: 'id', nullable: true)]
    private ?Area $maphostarea = null;

    /**
     * Map scope - world region. FD 573
     * If required, the world region covered by the map, expressed as a coded value. (ISO if existing)
     * @todo: add to forms
     */
    #[ORM\Column(name: 'map_scope_world_region', type: 'integer', length: 1, nullable: true)]
    private ?int $worldregion = null;

    /**
     * Map scope - N latitude. FD 274
     */
    #[ORM\Column(name: 'map_scope_N_latitude', type: 'decimal', precision: 11, scale: 7, nullable: true)]
    private ?string $northlatitude = null;

    /**
     * Map scope - S latitude. FD 275
     */
    #[ORM\Column(name: 'map_scope_S_latitude', type: 'decimal', precision: 11, scale: 7, nullable: true)]
    private ?string $southlatitude = null;

    /**
     * Map scope - E longitude. FD 276
     */
    #[ORM\Column(name: 'map_scope_E_longitude', type: 'decimal', precision: 11, scale: 7, nullable: true)]
    private ?string $eastlongitude = null;

    /**
     * Map scope - W longitude. FD 277
     */
    #[ORM\Column(name: 'map_scope_W_longitude', type: 'decimal', precision: 11, scale: 7, nullable: true)]
    private ?string $westlongitude = null;

    /**
     * Map edition. FD 557
     */
    #[Assert\Length(min: 1, max: 10, exactMessage: 'cave.validator.exact.length')]
    #[ORM\Column(name: 'map_edition', type: 'string', length: 10, nullable: true)]
    private ?string $edition = null;


    /**
     * Map latest update year. FD 273
     */
    #[Assert\Regex(pattern: '/^[0-9]{4}$/', message: 'cave.validator.match.regex', match: true)]
    #[ORM\Column(name: 'map_latest_update_year', type: 'integer', length: 4, nullable: true)]
    private ?int $latestupdateyear = null;

    /**
     * Map geog coords shown Y/N. FD 554
     */
    #[ORM\Column(name: 'map_geog_coords_shown_YN', type: 'boolean', nullable: true)]
    private ?bool $geogcoordsshown = null;
    /**
     * Map survey org ID. FD 203
     */
    #[ORM\ManyToOne(targetEntity: Organisation::class)]
    #[ORM\JoinColumn(name: 'map_survey_grade_org', referencedColumnName: 'id')]
    private ?Organisation $surveygradeorg = null;

    /**
     * Map & survey grade value. FD 204
     */
    #[ORM\Column(name: 'map_survey_grade_value', type: 'string', length: 8, nullable: true)]
    private ?string $surveygradevalue = null;

    /**
     * Map survey start year. FD 607
     */
    #[Assert\Length(min: 4, max: 4, exactMessage: 'cave.validator.exact.length')]
    #[ORM\Column(name: 'map_survey_start_year', type: 'smallint', length: 4, nullable: true, options: ['unsigned' => true, 'fixed' => true])]
    private ?int $surveystartyear = null;

    /**
     * Map survey finish year. FD 207
     */
    #[Assert\Length(min: 4, max: 4, exactMessage: 'cave.validator.exact.length')]
    #[ORM\Column(name: 'map_survey_finish_year', type: 'smallint', length: 4, nullable: true, options: ['unsigned' => true, 'fixed' => true])]
    private ?int $surveyfinishyear = null;

    /**
     * Map principal surveyor ID. FD 208
     */
    #[ORM\ManyToOne(targetEntity: Person::class)]
    #[ORM\JoinColumn(name: 'map_principal_surveyor_ID', referencedColumnName: 'id')]
    private ?Person $principalsurveyorid = null;

    /**
     * Map principal drafter ID. FD 402
     */
    #[ORM\ManyToOne(targetEntity: Person::class)]
    #[ORM\JoinColumn(name: 'map_principal_drafter_ID', referencedColumnName: 'id')]
    private ?Person $principaldrafterid = null;


    public function getId(): ?string
    {
        return $this->id??null;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): Map
    {
        $this->name = $name;
        return $this;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(?string $number): Map
    {
        $this->number = $number;
        return $this;
    }

    public function getSubsheetname(): ?string
    {
        return $this->subsheetname;
    }

    public function setSubsheetname(?string $subsheetname): Map
    {
        $this->subsheetname = $subsheetname;
        return $this;
    }

    public function getScale(): ?string
    {
        return $this->scale;
    }

    public function setScale(?string $scale): Map
    {
        $this->scale = $scale;
        return $this;
    }

    public function getType(): ?Fieldvaluecode
    {
        return $this->type;
    }

    public function setType(?Fieldvaluecode $type): Map
    {
        $this->type = $type;
        return $this;
    }

    public function getGeodeticdatum(): ?string
    {
        return $this->geodeticdatum;
    }

    public function setGeodeticdatum(?string $geodeticdatum): Map
    {
        $this->geodeticdatum = $geodeticdatum;
        return $this;
    }

    public function getHeightdatum(): ?string
    {
        return $this->heightdatum;
    }

    public function setHeightdatum(?string $heightdatum): Map
    {
        $this->heightdatum = $heightdatum;
        return $this;
    }

    public function getGrid(): ?string
    {
        return $this->grid;
    }

    public function setGrid(?string $grid): Map
    {
        $this->grid = $grid;
        return $this;
    }

    public function getSourcecountry(): ?Country
    {
        return $this->sourcecountry;
    }

    public function setSourcecountry(?Country $sourcecountry): Map
    {
        $this->sourcecountry = $sourcecountry;
        return $this;
    }

    public function getSourceorg(): ?Organisation
    {
        return $this->sourceorg;
    }

    public function setSourceorg(?Organisation $sourceorg): Map
    {
        $this->sourceorg = $sourceorg;
        return $this;
    }

    public function getSourcetype(): ?Fieldvaluecode
    {
        return $this->sourcetype;
    }

    public function setSourcetype(?Fieldvaluecode $sourcetype): Map
    {
        $this->sourcetype = $sourcetype;
        return $this;
    }

    public function getSourceifnoid(): ?string
    {
        return $this->sourceifnoid;
    }

    public function setSourceifnoid(?string $sourceifnoid): Map
    {
        $this->sourceifnoid = $sourceifnoid;
        return $this;
    }

    public function getCountry(): ?Country
    {
        return $this->country;
    }

    public function setCountry(?Country $country): Map
    {
        $this->country = $country;
        return $this;
    }

    public function getAdmin1(): ?Admin1
    {
        return $this->admin1;
    }

    public function setAdmin1(?Admin1 $admin1): Map
    {
        $this->admin1 = $admin1;
        return $this;
    }

    public function getAdmin2(): ?Admin2
    {
        return $this->admin2;
    }

    public function setAdmin2(?Admin2 $admin2): Map
    {
        $this->admin2 = $admin2;
        return $this;
    }

    public function getAdmin3(): ?Admin3
    {
        return $this->admin3;
    }

    public function setAdmin3(?Admin3 $admin3): Map
    {
        $this->admin3 = $admin3;
        return $this;
    }

    public function getArea(): ?Area
    {
        return $this->area;
    }

    public function setArea(?Area $area): Map
    {
        $this->area = $area;
        return $this;
    }

    public function getWorldregion(): ?int
    {
        return $this->worldregion;
    }

    public function setWorldregion(?int $worldregion): Map
    {
        $this->worldregion = $worldregion;
        return $this;
    }

    public function getMaphostarea(): ?Area
    {
        return $this->maphostarea;
    }

    public function setMaphostarea(?Area $maphostarea): Map
    {
        $this->maphostarea = $maphostarea;
        return $this;
    }

    public function getNorthlatitude(): ?string
    {
        return $this->northlatitude;
    }

    public function setNorthlatitude(?string $northlatitude): Map
    {
        $this->northlatitude = $northlatitude;
        return $this;
    }

    public function getSouthlatitude(): ?string
    {
        return $this->southlatitude;
    }

    public function setSouthlatitude(?string $southlatitude): Map
    {
        $this->southlatitude = $southlatitude;
        return $this;
    }

    public function getEastlongitude(): ?string
    {
        return $this->eastlongitude;
    }

    public function setEastlongitude(?string $eastlongitude): Map
    {
        $this->eastlongitude = $eastlongitude;
        return $this;
    }

    public function getWestlongitude(): ?string
    {
        return $this->westlongitude;
    }

    public function setWestlongitude(?string $westlongitude): Map
    {
        $this->westlongitude = $westlongitude;
        return $this;
    }

    public function getEdition(): ?string
    {
        return $this->edition;
    }

    public function setEdition(?string $edition): Map
    {
        $this->edition = $edition;
        return $this;
    }

    public function getLatestupdateyear(): ?int
    {
        return $this->latestupdateyear;
    }

    public function setLatestupdateyear(?int $latestupdateyear): Map
    {
        $this->latestupdateyear = $latestupdateyear;
        return $this;
    }

    public function getGeogcoordsshown(): ?bool
    {
        return $this->geogcoordsshown;
    }

    public function setGeogcoordsshown(?bool $geogcoordsshown): Map
    {
        $this->geogcoordsshown = $geogcoordsshown;
        return $this;
    }

    public function getSurveygradeorg(): ?Organisation
    {
        return $this->surveygradeorg;
    }

    public function setSurveygradeorg(?Organisation $surveygradeorg): Map
    {
        $this->surveygradeorg = $surveygradeorg;
        return $this;
    }

    public function getSurveygradevalue(): ?string
    {
        return $this->surveygradevalue;
    }

    public function setSurveygradevalue(?string $surveygradevalue): Map
    {
        $this->surveygradevalue = $surveygradevalue;
        return $this;
    }

    public function getSurveystartyear(): ?int
    {
        return $this->surveystartyear;
    }

    public function setSurveystartyear(?int $surveystartyear): Map
    {
        $this->surveystartyear = $surveystartyear;
        return $this;
    }

    public function getSurveyfinishyear(): ?int
    {
        return $this->surveyfinishyear;
    }

    public function setSurveyfinishyear(?int $surveyfinishyear): Map
    {
        $this->surveyfinishyear = $surveyfinishyear;
        return $this;
    }

    public function getPrincipalsurveyorid(): ?Person
    {
        return $this->principalsurveyorid;
    }

    public function setPrincipalsurveyorid(?Person $principalsurveyorid): Map
    {
        $this->principalsurveyorid = $principalsurveyorid;
        return $this;
    }

    public function getPrincipaldrafterid(): ?Person
    {
        return $this->principaldrafterid;
    }

    public function setPrincipaldrafterid(?Person $principaldrafterid): Map
    {
        $this->principaldrafterid = $principaldrafterid;
        return $this;
    }

    public function getMapserie(): ?Mapserie
    {
        return $this->mapserie;
    }

    public function setMapserie(?Mapserie $mapserie): Map
    {
        $this->mapserie = $mapserie;
        return $this;
    }

    public function getMap(): Map
    {
        return $this;
    }
}
