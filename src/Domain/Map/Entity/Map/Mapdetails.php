<?php
namespace  App\Domain\Map\Entity\Map;
use App\Domain\Map\Entity\Map\Model\MapOneToOneInterface;
use App\Domain\Map\Entity\Map\Trait\MapOneToOneTrait;
use App\Domain\Organisation\Entity\Organisation;
use App\Domain\Person\Entity\Person;
use  App\Infrastructure\Doctrine\Trait\CrupdatetimeTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * PL0203 Map details
 * Added from Map entity
 *  - 555:vectorversionavail
 *  - 556:rasterversionavail
 *  - 403:digitalmaster
 *  - 213:microfilmed
 * Pass to Map entity:
 *  - 203:surveygradeorg
 *  - 204:surveygradevalue
 *  - 206('10103:viewsshownp,10104:viewsshownl,10105:viewsshownx)
 *  - 607:surveystartyear
 *  - 207:surveyfinishyear
 *  - 208:principalsurveyorid
 *  - 402:principaldrafterid
 *  - 211:scopeadmin2areamap
 */
#[ORM\Table(name: 'map_details')]
#[ORM\Index(columns: ['map_numberer_org_code'], name: 'organisation_numberer_idx')]
#[ORM\Index(columns: ['map_biblio_updater_org_id'], name: 'organisation_updater_idx')]
#[ORM\Index(columns: ['map_biblio_updater_id'], name: 'person_updater_idx')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class Mapdetails implements MapOneToOneInterface
{
    use MapOneToOneTrait, CrupdatetimeTrait;

    /**
      * FD 195
      */
     #[ORM\Id]
     #[ORM\GeneratedValue(strategy: 'NONE')]
     #[ORM\OneToOne(targetEntity: Map::class)]
     #[ORM\JoinColumn(name: 'map', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
     private $map;

    /**
     * Map sequence number. FD 201
     */
    #[ORM\Column(name: 'map_sequence_number', type: 'string', length: 5, nullable: true)]
    private ?string $sequencenumber = null;   

     /**
     * Map cave serial number. FD 199
     */
    #[Assert\Range(min: 1, max: 9999)]
    #[ORM\Column(name: 'map_cave_serial_number', type: 'string', length: 4, nullable: true)]
    private ?string $caveserialnumber = null;

    /**
     * Codes to indicate what types of views the map presents from among plan (P), long sections (L), and cross-sections (X). FD 206
     */
    #[ORM\Column(name: 'map_views_shown', type: 'string', nullable: true)]
    #[Assert\Regex(pattern: '/\b(?!(?:.\B)*(.)(?:\B.)*\1)[PLX]+\b/', match: true)]
    private ?string $viewsshown = null;
    
    /**
     * Map details org ID. FD 517
     */
    #[ORM\ManyToOne(targetEntity: Organisation::class)]
    #[ORM\JoinColumn(name: 'map_numberer_org_code', referencedColumnName: 'id')]
    private ?Organisation $numbererorgcode = null;      

    /**
     * Map sheet size. FD 214
     */
    #[ORM\Column(name: 'sheetsize', type: 'string', length: 25, nullable: true)]
    private ?string $sheetsize = null;

    /**
     *  Map sheet quantity. FD 404
     */
    #[Assert\Range(min: 1, max: 9999)]
    #[ORM\Column(name: 'map_sheet_quantity', type: 'integer', length: 4, nullable: true, options: ['unsigned' => true, 'fixed' => true])]
    private ?int $sheetquantity = null;

    /**
     * Map issue finish year. FD 401
     */
    #[Assert\Range(min: 1, max: 9999)]
    #[ORM\Column(name: 'map_issue_year', type: 'integer', length: 4, nullable: true, options: ['unsigned' => true, 'fixed' => true])]
    private ?int $issueyear = null;   

    /**
     * Map restricted Y/N. FD 212
     */
    #[ORM\Column(name: 'map_restricted_YN', type: 'boolean', nullable: true)]
    private ?bool $restricted = null;

    /**
     * Map vector version avail?. FD 555
     */
    #[ORM\Column(name: 'map_vector_version_avail', type: 'boolean', nullable: true)]
    private ?bool $vectorversionavail = null;

    /**
     * Map raster version avail. FD 556
     */
    #[ORM\Column(name: 'map_raster_version_avail', type: 'boolean', nullable: true)]
    private ?bool $rasterversionavail = null;

    /**
     * Map restricted Y/N. FD 403
     */
    #[ORM\Column(name: 'map_digital_master_YN', type: 'boolean', nullable: true)]
    private ?bool $digitalmaster = null;

    /**
     * Map restricted Y/N. FD 213
     */
    #[ORM\Column(name: 'map_microfilmed_YN', type: 'boolean', nullable: true)]
    private ?bool $microfilmed = null;


    /**
     *  Map biblio updater name. FD 215
     */
    #[ORM\Column(name: 'map_biblio_updater', type: 'string', length: 20, nullable: true)]
    private ?string $updaterperson = null;

    /**
     * Map biblio updater ID. FD 580
     */
    #[ORM\ManyToOne(targetEntity: Person::class)]
    #[ORM\JoinColumn(name: 'map_biblio_updater_id', referencedColumnName: 'id')]
    private ?Person $updaterpersonid = null;

    /**
     * Map biblio updater org initials. FD 216. Use Organisation table if available
     */
    #[ORM\Column(name: 'map_biblio_updater_org', type: 'string', length: 12, nullable: true)]
    private ?string $updaterorg = null;

    /**
     * Map biblio updater org ID. FD 581
     */
    #[ORM\ManyToOne(targetEntity: Organisation::class)]
    #[ORM\JoinColumn(name: 'map_biblio_updater_org_id', referencedColumnName: 'id')]
    private ?Organisation $updaterorgid = null;

    /**
     * Map biblio updater year. FD 217
     */
    #[Assert\Range(min: 1, max: 9999)]
    #[ORM\Column(name: 'map_biblio_updater_year', type: 'smallint', nullable: true, options: ['unsigned' => true, 'fixed' => true])]
    private ?int $updateryear = null;

    public function getSequencenumber(): ?string
    {
        return $this->sequencenumber;
    }

    public function setSequencenumber(?string $sequencenumber): Mapdetails
    {
        $this->sequencenumber = $sequencenumber;
        return $this;
    }

    public function getCaveserialnumber(): ?string
    {
        return $this->caveserialnumber;
    }

    public function setCaveserialnumber(?string $caveserialnumber): Mapdetails
    {
        $this->caveserialnumber = $caveserialnumber;
        return $this;
    }

    public function getViewsshown(): ?string
    {
        return $this->viewsshown;
    }

    public function setViewsshown(?string $viewsshown): Mapdetails
    {
        $this->viewsshown = $viewsshown;
        return $this;
    }

    public function getNumbererorgcode(): ?Organisation
    {
        return $this->numbererorgcode;
    }

    public function setNumbererorgcode(?Organisation $numbererorgcode): Mapdetails
    {
        $this->numbererorgcode = $numbererorgcode;
        return $this;
    }

    public function getSheetsize(): ?string
    {
        return $this->sheetsize;
    }

    public function setSheetsize(?string $sheetsize): Mapdetails
    {
        $this->sheetsize = $sheetsize;
        return $this;
    }

    public function getSheetquantity(): ?int
    {
        return $this->sheetquantity;
    }

    public function setSheetquantity(?int $sheetquantity): Mapdetails
    {
        $this->sheetquantity = $sheetquantity;
        return $this;
    }

    public function getIssueyear(): ?int
    {
        return $this->issueyear;
    }

    public function setIssueyear(?int $issueyear): Mapdetails
    {
        $this->issueyear = $issueyear;
        return $this;
    }

    public function isRestricted(): ?bool
    {
        return $this->restricted;
    }

    public function setRestricted(?bool $restricted): Mapdetails
    {
        $this->restricted = $restricted? true : null;
        return $this;
    }

    public function isVectorversionavail(): ?bool
    {
        return $this->vectorversionavail;
    }

    public function setVectorversionavail(?bool $vectorversionavail): Mapdetails
    {
        $this->vectorversionavail = $vectorversionavail? true : null;
        return $this;
    }

    public function isRasterversionavail(): ?bool
    {
        return $this->rasterversionavail;
    }

    public function setRasterversionavail(?bool $rasterversionavail): Mapdetails
    {
        $this->rasterversionavail = $rasterversionavail? true : null;
        return $this;
    }

    public function isDigitalmaster(): ?bool
    {
        return $this->digitalmaster;
    }

    public function setDigitalmaster(?bool $digitalmaster): Mapdetails
    {
        $this->digitalmaster = $digitalmaster? true : null;
        return $this;
    }

    public function isMicrofilmed(): ?bool
    {
        return $this->microfilmed;
    }

    public function setMicrofilmed(?bool $microfilmed): Mapdetails
    {
        $this->microfilmed = $microfilmed? true : null;
        return $this;
    }

    public function getUpdaterperson(): ?string
    {
        return $this->updaterperson;
    }

    public function setUpdaterperson(?string $updaterperson): Mapdetails
    {
        $this->updaterperson = $updaterperson;
        return $this;
    }

    public function getUpdaterpersonid(): ?Person
    {
        return $this->updaterpersonid;
    }

    public function setUpdaterpersonid(?Person $updaterpersonid): Mapdetails
    {
        $this->updaterpersonid = $updaterpersonid;
        return $this;
    }

    public function getUpdaterorg(): ?string
    {
        return $this->updaterorg;
    }

    public function setUpdaterorg(?string $updaterorg): Mapdetails
    {
        $this->updaterorg = $updaterorg;
        return $this;
    }

    public function getUpdaterorgid(): ?Organisation
    {
        return $this->updaterorgid;
    }

    public function setUpdaterorgid(?Organisation $updaterorgid): Mapdetails
    {
        $this->updaterorgid = $updaterorgid;
        return $this;
    }

    public function getUpdateryear(): ?int
    {
        return $this->updateryear;
    }

    public function setUpdateryear(?int $updateryear): Mapdetails
    {
        $this->updateryear = $updateryear;
        return $this;
    }

}

