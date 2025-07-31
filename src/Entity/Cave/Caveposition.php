<?php
namespace  App\Entity\Cave;
use App\Domain\Fielddefinition\Entity\Fieldvaluecode;
use App\Entity\Cave\Trait\CaveOneToOneTrait;
use App\Entity\CommonTrait\CrupdatetimeTrait;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * CA0245 Exact position 0-1.
 * This table stores the current best estimate of
 * the cave's position using any or all lat/long,
 * grid co-ords, altitude and cadastral.
 */
#[ORM\Table(name: 'cave_position')]
#[ORM\Index(columns: ['position_grid_ref_units'], name: 'fieldvaluecode_position_grid_ref_units_idx')]
#[ORM\Index(columns: ['geographic_method'], name: 'fieldvaluecode_geographic_method_idx')]
#[ORM\Index(columns: ['gref_method'], name: 'fieldvaluecode_gref_method_idx')]
#[ORM\Index(columns: ['altitude_method'], name: 'fieldvaluecode_altitude_method_idx')]
#[ORM\Index(columns: ['altitude_units'], name: 'fieldvaluecode_altitude_units_idx')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class Caveposition
{
    use CaveOneToOneTrait, CrupdatetimeTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    #[ORM\OneToOne(inversedBy: 'caveposition', targetEntity: Cave::class)]
    #[ORM\JoinColumn(name: 'cave', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private Cave $cave;
    
    /**
     * FD 245
     */
    #[ORM\Column(name: 'position_latitude', type: 'decimal', precision: 11, scale: 7, nullable: true)]
    private ?string $latitude = null;

    /**
     * FD 246
     */
    #[ORM\Column(name: 'position_longitude', type: 'decimal', precision: 12, scale: 7, nullable: true)]
    private ?string $longitude = null;

    /**
     * FD 514
     */
    #[ORM\Column(name: 'position_geog_precision', type: 'decimal', precision: 8, scale: 5, nullable: true)]
    private ?float $geogprecision = null;

    /**
     * FD 515
     */
    #[ORM\Column(name: 'position_geog_accuracy', type: 'decimal', precision: 8, scale: 5, nullable: true)]
    private ?float $geogaccuracy = null;

    /**
     * FD 247. UTM zone designatn
     */
    #[ORM\Column(name: 'position_grid_zone', type: 'string', length: 3, nullable: true)]
    private ?string $gridzone = null;

    /**
     * FD 249
     */
    #[ORM\Column(name: 'position_easting', type: 'decimal', precision: 10, scale: 3, nullable: true)]
    private ?float $easting = null;

    /**
     * FD 250
     */
    #[ORM\Column(name: 'position_northing', type: 'decimal', precision: 12, scale: 3, nullable: true)]
    private ?float $northing = null;

    /**
     * FD 251
     */
    #[ORM\ManyToOne(targetEntity: Fieldvaluecode::class)]
    #[ORM\JoinColumn(name: 'position_grid_ref_units', referencedColumnName: 'id')]
    private ?Fieldvaluecode $gridrefunits = null;

    /**
     * FD 252
     */
    #[ORM\Column(name: 'position_gref_precision', type: 'decimal', precision: 10, scale: 3, nullable: true)]
    private ?float $grefprecision = null;

    /**
     * FD 300
     */
    #[ORM\Column(name: 'position_gref_accuracy', type: 'decimal', precision: 10, scale: 3, nullable: true)]
    private ?float $grefaccuracy = null;

    /**
     * FD 625
     */
    #[ORM\Column(name: 'geographic_geodetic_datum', type: 'string', length: 30, nullable: true)]
    private ?string $geodeticdatum = null;

    /**
     * FD 650
     */
    #[ORM\Column(name: 'gref_geodetic_datum', type: 'string', length: 30, nullable: true)]
    private ?string $grefdatum = null;    

    /**
     * FD 626
     */
    #[ORM\Column(name: 'position_map_grid', type: 'string', length: 30, nullable: true)]
    private ?string $mapgrid = null;

    /**
     * FD 628
     */
    #[ORM\ManyToOne(targetEntity: Fieldvaluecode::class)]
    #[ORM\JoinColumn(name: 'geographic_method', referencedColumnName: 'id')]
    private ?Fieldvaluecode $geographicmethod = null;
    //END CAVE POSITION
    /**
     * FD 651
     */
    #[ORM\ManyToOne(targetEntity: Fieldvaluecode::class)]
    #[ORM\JoinColumn(name: 'gref_method', referencedColumnName: 'id')]
    private ?Fieldvaluecode $grefmethod = null;    

    /**
     * FD 631
     */
    #[ORM\Column(name: 'geographic_date', type: 'datetime', nullable: true, options: ['unsigned' => true])]
    private ?DateTime $geographicdate = null;

    /**
     * FD 652
     */
    #[ORM\Column(name: 'gref_date', type: 'datetime', length: 4, nullable: true, options: ['unsigned' => true])]
    private ?DateTime $grefdate = null;

    /**
     * FD 442
     */
    #[ORM\Column(name: 'altitude', type: 'decimal', precision: 10, scale: 3, nullable: true)]
    private ?float $altitude = null;

    /**
     * FD 661
     */
    #[ORM\ManyToOne(targetEntity: Fieldvaluecode::class)]
    #[ORM\JoinColumn(name: 'altitude_method', referencedColumnName: 'id')]
    private ?Fieldvaluecode $altitudemethod = null;

    /**
     * FD 443
     */
    #[ORM\ManyToOne(targetEntity: Fieldvaluecode::class)]
    #[ORM\JoinColumn(name: 'altitude_units', referencedColumnName: 'id')]
    private ?Fieldvaluecode $altitudeunits = null;

    /**
     * FD 444
     */
    #[ORM\Column(name: 'altitude_precision', type: 'decimal', precision: 8, scale: 3, nullable: true)]
    private ?float $altitudeprecision = null;

    /**
     * FD 445
     */
    #[ORM\Column(name: 'altitude_accuracy', type: 'decimal', precision: 8, scale: 3, nullable: true)]
    private ?float $altitudeaccuracy = null;

    /**
     * FD 627
     */
    #[ORM\Column(name: 'altitude_height_datum', type: 'string', length: 30, nullable: true)]
    private ?string $altitudeheightdatum = null;

    /**
     * FD 649
     */
    #[ORM\Column(name: 'position_geog_comment', type: 'string', length: 255, nullable: true)]
    private ?string $geographiccomment = null;

    /**
     * FD 660.  Grid reference comment
     */
    #[ORM\Column(name: 'position_gref_comment', type: 'string', length: 255, nullable: true)]
    private ?string $grefcomment = null;

    /**
     * FD 670
     */
    #[ORM\Column(name: 'altitude_comment', type: 'string', length: 255, nullable: true)]
    private ?string $altitudecomment = null;      

    /**
     * FD 673 use gridzone
     */
    #[ORM\Column(name: 'utm_zone_number', type: 'string', length: 2, nullable: true)]
    private ?string $utmzonenumber = null;

    /**
     * FD 674 use $gridzone
     */
    #[ORM\Column(name: 'utm_zone_letter', type: 'string', length: 1, nullable: true)]
    private ?string $utmzoneletter = null;

    /**
     * FD 676. En UIS como position_sheet_number 100K sheet number
     */
    #[ORM\Column(name: 'position_sheet_100', type: 'string', length: 8, nullable: true)]
    private ?string $sheet100 = null;

    /**
     * FD 677. En UIS como 25K sheet number
     */
    #[ORM\Column(name: 'position_sheet_25', type: 'string', length: 16, nullable: true)]
    private ?string $sheet25 = null;

    /**
     * FD 441.  State. Cadastral lands units (State). Use Admin1
     */
    #[ORM\Column(name: 'fifthsmallest_land_unit', type: 'string', length: 30, nullable: true)]
    private ?string $landunit5 = null;

    /**
     * FD 253.  County ~ province. Use Admin2
     */
    #[ORM\Column(name: 'fourthsmallest_land_unit', type: 'string', length: 30, nullable: true)]
    private ?string $landunit4 = null;

    /**
     * FD 254. Parish ~ municipality. Use Admin3
     */
    #[ORM\Column(name: 'thirdsmallest_land_unit', type: 'string', length: 30, nullable: true)]
    private ?string $landunit3 = null;

    /**
     * FD 255. Land unit parcel section.
     */
    #[ORM\Column(name: 'secondsmallest_land_unit', type: 'string', length: 30, nullable: true)]
    private ?string $landunit2 = null;

    /**
     * FD 256. Land unit lot (allotments > section).
     */
    #[ORM\Column(name: 'smallest_land_unit', type: 'string', length: 30, nullable: true)]
    private ?string $landunit1 = null;

    /**
     * FD 365. Special land unit. Case where the parcel nomenclature is not normal.
     */
    #[ORM\Column(name: 'special_land_parcel', type: 'string', length: 30, nullable: true)]
    private ?string $landunit0 = null;

    public function getLatitude(): ?string
    {
        return $this->latitude;
    }
 
    public function setLatitude(?string $latitude): Caveposition
    {
        $this->latitude = $latitude;
        return $this;
    }
 
    public function getLongitude(): ?string
    {
        return $this->longitude;
    }
 
    public function setLongitude(?string $longitude): Caveposition
    {
        $this->longitude = $longitude;
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
 
    public function setGeogprecision(?float $geogprecision): Caveposition
    {
        $this->geogprecision = $geogprecision;
        return $this;
    }

    /**
     * Decimal fields cannot be type-hinted as float
     * @link https://github.com/symfony/symfony/issues/32124
     */
    public function getGeogaccuracy(): ?string
    {
        return $this->geogaccuracy;
    }
 
    public function setGeogaccuracy(?float $geogaccuracy): Caveposition
    {
        $this->geogaccuracy = $geogaccuracy;
        return $this;
    }
 
    public function getGridzone(): ?string
    {
        return $this->gridzone;
    }
 
    public function setGridzone(?string $gridzone): Caveposition
    {
        $this->gridzone = $gridzone;
        return $this;
    }

    /**
     * Decimal fields cannot be type-hinted as float
     * @link https://github.com/symfony/symfony/issues/32124
      */
    public function getEasting(): ?string
    {
        return $this->easting;
    }
 
    public function setEasting(?float $easting): Caveposition
    {
        $this->easting = $easting;
        return $this;
    }

    /**
     * Decimal fields cannot be type-hinted as float
     * @link https://github.com/symfony/symfony/issues/32124
      */
    public function getNorthing(): ?string
    {
        return $this->northing;
    }
 
    public function setNorthing(?float $northing): Caveposition
    {
        $this->northing = $northing;
        return $this;
    }

    public function getGridrefunits(): ?Fieldvaluecode
    {
        return $this->gridrefunits;
    }
 
    public function setGridrefunits(?Fieldvaluecode $gridrefunits): Caveposition
    {
        $this->gridrefunits = $gridrefunits;
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
 
    public function setGrefprecision(?float $grefprecision): Caveposition
    {
        $this->grefprecision = $grefprecision;
        return $this;
    }

    /**
     * Decimal fields cannot be type-hinted as float
     * @link https://github.com/symfony/symfony/issues/32124
      */
    public function getGrefaccuracy(): ?string
    {
        return $this->grefaccuracy;
    }
 
    public function setGrefaccuracy(?float $grefaccuracy): Caveposition
    {
        $this->grefaccuracy = $grefaccuracy;
        return $this;
    }
 
    public function getGeodeticdatum(): ?string
    {
        return $this->geodeticdatum;
    }
 
    public function setGeodeticdatum(?string $geodeticdatum): Caveposition
    {
        $this->geodeticdatum = $geodeticdatum;
        return $this;
    }
 
    public function getGrefdatum(): ?string
    {
        return $this->grefdatum;
    }
 
    public function setGrefdatum(?string $grefdatum): Caveposition
    {
        $this->grefdatum = $grefdatum;
        return $this;
    }
 
    public function getMapgrid(): ?string
    {
        return $this->mapgrid;
    }
 
    public function setMapgrid(?string $mapgrid): Caveposition
    {
        $this->mapgrid = $mapgrid;
        return $this;
    }
 
    public function getGeographicmethod(): ?Fieldvaluecode
    {
        return $this->geographicmethod;
    }
 
    public function setGeographicmethod(?Fieldvaluecode $geographicmethod): Caveposition
    {
        $this->geographicmethod = $geographicmethod;
        return $this;
    }
 
    public function getGrefmethod(): ?Fieldvaluecode
    {
        return $this->grefmethod;
    }
 
    public function setGrefmethod(?Fieldvaluecode $grefmethod): Caveposition
    {
        $this->grefmethod = $grefmethod;
        return $this;
    }
 
    public function getGeographicdate(): ?DateTime
    {
        return $this->geographicdate;
    }
 
    public function setGeographicdate(?DateTime $geographicdate): Caveposition
    {
        $this->geographicdate = $geographicdate;
        return $this;
    }
 
    public function getGrefdate(): ?DateTime
    {
        return $this->grefdate;
    }
 
    public function setGrefdate(?DateTime $grefdate): Caveposition
    {
        $this->grefdate = $grefdate;
        return $this;
    }

    /**
     * Decimal fields cannot be type-hinted as float
     * @link https://github.com/symfony/symfony/issues/32124
      */
    public function getAltitude(): ?string
    {
        return $this->altitude;
    }
 
    public function setAltitude(?float $altitude): Caveposition
    {
        $this->altitude = $altitude;
        return $this;
    }
 
    public function getAltitudemethod(): ?Fieldvaluecode
    {
        return $this->altitudemethod;
    }
 
    public function setAltitudemethod(?Fieldvaluecode $altitudemethod): Caveposition
    {
        $this->altitudemethod = $altitudemethod;
        return $this;
    }
 
    public function getAltitudeunits(): ?Fieldvaluecode
    {
        return $this->altitudeunits;
    }
 
    public function setAltitudeunits(?Fieldvaluecode $altitudeunits): Caveposition
    {
        $this->altitudeunits = $altitudeunits;
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
 
    public function setAltitudeprecision(?float $altitudeprecision): Caveposition
    {
        $this->altitudeprecision = $altitudeprecision;
        return $this;
    }

    /**
     * Decimal fields cannot be type-hinted as float
     * @link https://github.com/symfony/symfony/issues/32124
      */
    public function getAltitudeaccuracy(): ?string
    {
        return $this->altitudeaccuracy;
    }
 
    public function setAltitudeaccuracy(?float $altitudeaccuracy): Caveposition
    {
        $this->altitudeaccuracy = $altitudeaccuracy;
        return $this;
    }
 
    public function getAltitudeheightdatum(): ?string
    {
        return $this->altitudeheightdatum;
    }
 
    public function setAltitudeheightdatum(?string $altitudeheightdatum): Caveposition
    {
        $this->altitudeheightdatum = $altitudeheightdatum;
        return $this;
    }
 
    public function getGeographiccomment(): ?string
    {
        return $this->geographiccomment;
    }
 
    public function setGeographiccomment(?string $geographiccomment): Caveposition
    {
        $this->geographiccomment = $geographiccomment;
        return $this;
    }
 
    public function getGrefcomment(): ?string
    {
        return $this->grefcomment;
    }
 
    public function setGrefcomment(?string $grefcomment): Caveposition
    {
        $this->grefcomment = $grefcomment;
        return $this;
    }
 
    public function getAltitudecomment(): ?string
    {
        return $this->altitudecomment;
    }
 
    public function setAltitudecomment(?string $altitudecomment): Caveposition
    {
        $this->altitudecomment = $altitudecomment;
        return $this;
    }
 
    public function getUtmzonenumber(): ?string
    {
        return $this->utmzonenumber;
    }
 
    public function setUtmzonenumber(?string $utmzonenumber): Caveposition
    {
        $this->utmzonenumber = $utmzonenumber;
        return $this;
    }
 
    public function getUtmzoneletter(): ?string
    {
        return $this->utmzoneletter;
    }
 
    public function setUtmzoneletter(?string $utmzoneletter): Caveposition
    {
        $this->utmzoneletter = $utmzoneletter;
        return $this;
    }
 
    public function getSheet100(): ?string
    {
        return $this->sheet100;
    }
 
    public function setSheet100(?string $sheet100): Caveposition
    {
        $this->sheet100 = $sheet100;
        return $this;
    }
 
    public function getSheet25(): ?string
    {
        return $this->sheet25;
    }
 
    public function setSheet25(?string $sheet25): Caveposition
    {
        $this->sheet25 = $sheet25;
        return $this;
    }
 
    public function getLandunit5(): ?string
    {
        return $this->landunit5;
    }
 
    public function setLandunit5(?string $landunit5): Caveposition
    {
        $this->landunit5 = $landunit5;
        return $this;
    }
 
    public function getLandunit4(): ?string
    {
        return $this->landunit4;
    }
 
    public function setLandunit4(?string $landunit4): Caveposition
    {
        $this->landunit4 = $landunit4;
        return $this;
    }
 
    public function getLandunit3(): ?string
    {
        return $this->landunit3;
    }
 
    public function setLandunit3(?string $landunit3): Caveposition
    {
        $this->landunit3 = $landunit3;
        return $this;
    }
 
    public function getLandunit2(): ?string
    {
        return $this->landunit2;
    }
 
    public function setLandunit2(?string $landunit2): Caveposition
    {
        $this->landunit2 = $landunit2;
        return $this;
    }
 
    public function getLandunit1(): ?string
    {
        return $this->landunit1;
    }
 
    public function setLandunit1(?string $landunit1): Caveposition
    {
        $this->landunit1 = $landunit1;
        return $this;
    }
 
    public function getLandunit0(): ?string
    {
        return $this->landunit0;
    }
 
    public function setLandunit0(?string $landunit0): Caveposition
    {
        $this->landunit0 = $landunit0;
        return $this;
    }
}

