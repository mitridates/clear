<?php
namespace  App\Entity\Cave;
use App\Domain\Map\Entity\Map\Map;
use App\Entity\Cave\Trait\CaveManyToOneTrait;
use App\Entity\CommonTrait\{CrupdatetimeTrait};
use App\Entity\CommonTrait\SequenceTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * CA0055 Widest map 0:n
 */
#[ORM\Table(name: 'cave_widestmap')]
#[ORM\Index(columns: ['cave'], name: 'cave_idx')]
#[ORM\Index(columns: ['grid_ref_map_id'], name: 'grid_ref_map_idx')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class Cavewidestmap
{
    use SequenceTrait, CrupdatetimeTrait, CaveManyToOneTrait;

    /**
      * FD 227
      */
     #[ORM\ManyToOne(targetEntity: Cave::class, inversedBy: 'cavewidestmap')]
     #[ORM\JoinColumn(name: 'cave', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
     private Cave $cave;

    /**
     * FD 265
     */
    #[ORM\ManyToOne(targetEntity:  Map::class)]
    #[ORM\JoinColumn(name: 'grid_ref_map_id', referencedColumnName: 'id', nullable: true)]
    private ?Map $map = null;

    /**
     *  Map source - org code. FD 55
     */
    #[ORM\Column(name: 'map_source_org_code', type: 'string', length: 3, nullable: true)]
    private ?string $sourceorgcode = null;  

    /**
     * Map sequence number. FD 264
     */
    #[ORM\Column(name: 'map_sequence_number', type: 'string', length: 5, nullable: true)]
    private ?string $sequencenumber = null;         

    /**
     * Map or sheet name. FD 359
     */
    #[ORM\Column(name: 'map_or_sheet_name', type: 'string', length: 30, nullable: true)]
    private ?string $maporsheetname = null;
    /**
     * Survey grade org. FD 360
     */
    #[ORM\Column(name: 'map_survey_grade_org', type: 'string', length: 4, nullable: true)]
    private ?string $surveygradeorg = null;    

    /**
     * Survey grade value. FD 361
     */
    #[ORM\Column(name: 'map_survey_grade_value', type: 'string', length: 8, nullable: true)]
    private ?string $surveygradevalue = null;  

    /**
     * FD 362
     */
    #[ORM\Column(name: 'map_scale', type: 'decimal', precision: 10, scale: 0, nullable: true)]
    private ?float $scale = null;

    /**
     * Survey date, map issue date, and date of latest map amendment. FD 363
     */
    #[ORM\Column(name: 'survissueamend', type: 'string', length: 15, nullable: true)]
    private ?string $survissueamend = null;

    public function getMap(): ?Map
    {
        return $this->map;
    }

    public function setMap(?Map $map): Cavewidestmap
    {
        $this->map = $map;
        return $this;
    }

    public function getSourceorgcode(): ?string
    {
        return $this->sourceorgcode;
    }

    public function setSourceorgcode(?string $sourceorgcode): Cavewidestmap
    {
        $this->sourceorgcode = $sourceorgcode;
        return $this;
    }

    public function getSequencenumber(): ?string
    {
        return $this->sequencenumber;
    }

    public function setSequencenumber(?string $sequencenumber): Cavewidestmap
    {
        $this->sequencenumber = $sequencenumber;
        return $this;
    }

    public function getMaporsheetname(): ?string
    {
        return $this->maporsheetname;
    }

    public function setMaporsheetname(?string $maporsheetname): Cavewidestmap
    {
        $this->maporsheetname = $maporsheetname;
        return $this;
    }

    public function getSurveygradeorg(): ?string
    {
        return $this->surveygradeorg;
    }

    public function setSurveygradeorg(?string $surveygradeorg): Cavewidestmap
    {
        $this->surveygradeorg = $surveygradeorg;
        return $this;
    }

    public function getSurveygradevalue(): ?string
    {
        return $this->surveygradevalue;
    }

    public function setSurveygradevalue(?string $surveygradevalue): Cavewidestmap
    {
        $this->surveygradevalue = $surveygradevalue;
        return $this;
    }

    /**
     * Decimal fields cannot be type-hinted as float
     * @link https://github.com/symfony/symfony/issues/32124
     */
    public function getScale(): ?string
    {
        return $this->scale;
    }

    public function setScale(?float $scale): Cavewidestmap
    {
        $this->scale = $scale;
        return $this;
    }

    public function getSurvissueamend(): ?string
    {
        return $this->survissueamend;
    }

    public function setSurvissueamend(?string $survissueamend): Cavewidestmap
    {
        $this->survissueamend = $survissueamend;
        return $this;
    }
}
