<?php
namespace  App\Mapserie\Domain\Entity;
use App\Fielddefinition\Domain\Entity\Fieldvaluecode;
use App\Organisation\Domain\Entity\Organisation;
use App\Shared\Doctrine\Orm\Id\CavernIdGenerator;
use App\Shared\Doctrine\Trait\CrupdatetimeTrait;
use App\Shared\Doctrine\Trait\HiddenTrait;
use Doctrine\ORM\Mapping as ORM;

;

/**
 * Map Series (PS0000)
 * @link http://kid.caves.org.au/kid/doc/table_relationships?entity=PS
 */
#[ORM\Table(name: 'map_serie')]
#[ORM\Index(columns: ['map_series_length_units'], name: 'fieldvaluecode_length_units_idx')]
#[ORM\Index(columns: ['map_series_map_type'], name: 'fieldvaluecode_map_type_idx')]
#[ORM\Index(columns: ['map_series_publisher_ID'], name: 'organisation_publisher_idx')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class Mapserie
{
    use HiddenTrait, CrupdatetimeTrait;

    /**
     * FD - uncoded
     */
    #[ORM\Id]
    #[ORM\Column(name: 'id', type: 'string', length: 17, nullable: false)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: CavernIdGenerator::class)]
    private ?string $id=null;

    /**
     * Map series code.FD 278
     */
    #[ORM\Column(name: 'map_series_code', type: 'string', length: 12, nullable: true)]
    private ?string $code = null;
    /**
     * Map series name. FD 279
     */
    #[ORM\Column(name: 'map_series_name', type: 'string', length: 62, nullable: true)]
    private ?string $name = null;
    /**
     * Map series abbreviation. FD 372
     */
    #[ORM\Column(name: 'map_series_abbreviation', type: 'string', length: 12, nullable: true)]
    private ?string $abbreviation = null;
    /**
     * Map series length units. FD 280. Codes 298.
     */
    #[ORM\ManyToOne(targetEntity: Fieldvaluecode::class)]
    #[ORM\JoinColumn(name: 'map_series_length_units', referencedColumnName: 'id')]
    private ?Fieldvaluecode $lengthunits = null;

    /**
     * Map series scale. FD 373
     */
    #[ORM\Column(name: 'map_series_scale', type: 'decimal', precision: 10, scale: 0, nullable: true)]
    private ?string $scale = null;
    /**
     * Map series map type. FD 559. Codes 367.
     */
    #[ORM\ManyToOne(targetEntity: Fieldvaluecode::class)]
    #[ORM\JoinColumn(name: 'map_series_map_type', referencedColumnName: 'id')]
    private ?Fieldvaluecode $maptype = null;

    /**
     * Publisher Organisation. Map series publisher ID. FD 374.
     */
    #[ORM\ManyToOne(targetEntity: Organisation::class)]
    #[ORM\JoinColumn(name: 'map_series_publisher_ID', referencedColumnName: 'id')]
    private ?Organisation $publisher = null;

    /**
     * FD uncoded
     */
    #[ORM\Column(name: 'comment', type: 'string', length: 255, nullable: true)]
    private ?string $comment = null;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): Mapserie
    {
        $this->code = $code;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): Mapserie
    {
        $this->name = $name;
        return $this;
    }

    public function getAbbreviation(): ?string
    {
        return $this->abbreviation;
    }

    public function setAbbreviation(?string $abbreviation): Mapserie
    {
        $this->abbreviation = $abbreviation;
        return $this;
    }

    public function getLengthunits(): ?Fieldvaluecode
    {
        return $this->lengthunits;
    }

    public function setLengthunits(?Fieldvaluecode $lengthunits): Mapserie
    {
        $this->lengthunits = $lengthunits;
        return $this;
    }

    public function getScale(): ?string
    {
        return $this->scale;
    }

    public function setScale(?string $scale): Mapserie
    {
        $this->scale = $scale;
        return $this;
    }

    public function getMaptype(): ?Fieldvaluecode
    {
        return $this->maptype;
    }

    public function setMaptype(?Fieldvaluecode $maptype): Mapserie
    {
        $this->maptype = $maptype;
        return $this;
    }

    public function getPublisher(): ?Organisation
    {
        return $this->publisher;
    }

    public function setPublisher(?Organisation $publisher): Mapserie
    {
        $this->publisher = $publisher;
        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): Mapserie
    {
        $this->comment = $comment;
        return $this;
    }
}
