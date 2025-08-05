<?php
namespace  App\Cave\Domain\Entity;
use App\Cave\Domain\Entity\Trait\CaveManyToOneTrait;
use App\Fielddefinition\Domain\Entity\Fieldvaluecode;
use App\Map\Domain\Entity\Map\Map;
use App\Shared\Doctrine\Trait\CrupdatetimeTrait;
use App\Shared\Doctrine\Trait\SequenceTrait;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * CA0241 Position on maps (uisic.uis-speleo.org) === Cave grid references (kid.caves.org.au)
 * @see https://kid.caves.org.au//kid/doc/table_relationships?entity=CA&table=CA0241
 * @see http://www.uisic.uis-speleo.org/exchange/catables.html
 */
#[ORM\Table(name: 'cave_grid')]
#[ORM\Index(columns: ['cave'], name: 'cave_idx')]
#[ORM\Index(columns: ['grid_ref_map_id'], name: 'map_idx')]
#[ORM\Index(columns: ['grid_ref_units'], name: 'Fieldvaluecode_grid_ref_units_idx')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class Cavegrid
{
    use SequenceTrait, CrupdatetimeTrait, CaveManyToOneTrait;

    /**
      * FD 227
      */
     #[ORM\ManyToOne(targetEntity: Cave::class, inversedBy: 'cavegrid')]
     #[ORM\JoinColumn(name: 'cave', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
     private Cave $cave;

    /**
     * FD 411
     */
    #[ORM\ManyToOne(targetEntity:  Map::class)]
    #[ORM\JoinColumn(name: 'grid_ref_map_id', referencedColumnName: 'id', nullable: true)]
    private ?Map $map = null;

    /**
     * Gref. easting. FD 241
     */
    #[ORM\Column(name: 'grid_ref_easting', type: 'decimal', precision: 10, scale: 0, nullable: true)]
    private ?float $easting = null;

    /**
     * Map northing. FD 242
     */
    #[ORM\Column(name: 'grid_ref_northing', type: 'decimal', precision: 10, scale: 0, nullable: true)]
    private ?float $northing = null;

    /**
     * FD 243
     */
    #[ORM\ManyToOne(targetEntity: Fieldvaluecode::class)]
    #[ORM\JoinColumn(name: 'grid_ref_units', referencedColumnName: 'id', nullable: true)]
    private ?Fieldvaluecode $units = null;

    /**
     * Gref. precision. FD 244
     */
    #[ORM\Column(name: 'grid_ref_precision_used', type: 'decimal', precision: 10, scale: 0, nullable: true)]
    private ?float $precision = null;

    /**
     * Gref. accuracy. FD 302
     */
    #[ORM\Column(name: 'grid_ref_accuracy_used', type: 'decimal', precision: 10, scale: 0, nullable: true)]
    private ?float $accuracy = null;

    /**
     * Map posn - map edition. FD 240
     */
    #[ORM\Column(name: 'map_edition', type: 'string', length: 8, nullable: true)]
    private ?string $edition = null;

    /**
     * Grid ref - date. FD 632
     */
    #[ORM\Column(name: 'grid_ref_date', type: 'datetime', nullable: true)]
    private ?DateTime $date = null;

//  Only present if not avail by 411 direct link (Map)
    //    /**
    //     * Geodetic datum. FD 629
    //     * @ORM\Column(name="grid_ref_geodetic_datum", type="string", length=30, nullable=true)
    //     */
    //    private $geodeticdatum;
    //
    //    /**
    //     * Map grid. FD 630
    //     * @ORM\Column(name="grid_ref_map_grid", type="string", length=30, nullable=true)
    //     */
    //    private $mapgrid;
    //
    //    /**
    //     * Map scale. FD 238
    //     * @ORM\Column(name="grid_ref_map_scale", type="decimal", precision=10, scale=1, nullable=true)
    //     */
    //    private $mapscale;
    //
    //    /**
    //     * Map posn - map number. FD 239
    //     * @ORM\Column(name="grid_ref_map_number", type="string", length=25, nullable=true)
    //     */
    //    private $mapnumber;
    //
    //    /**
    //     * FD 414
    //     * @Assert\Length(
    //     *      max = 30,
    //     *      maxMessage= "cave.validator.max.length"
    //     * )
    //     * @ORM\Column(name="grid_ref_map_name", type="string", length=30, nullable=true)
    //     */
    //    private $mapname;
    /**
     * Map posn - comment. FD 640
     */
    #[ORM\Column(name: 'map_pos_comment', type: 'string', length: 255, nullable: true)]
    private ?string $comment = null;

    public function getMap(): ?Map
    {
        return $this->map;
    }

    public function setMap(?Map $map): Cavegrid
    {
        $this->map = $map;
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

    public function setEasting(?float $easting): Cavegrid
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

    public function setNorthing(?float $northing): Cavegrid
    {
        $this->northing = $northing;
        return $this;
    }

    public function getUnits(): ?Fieldvaluecode
    {
        return $this->units;
    }

    public function setUnits(?Fieldvaluecode $units): Cavegrid
    {
        $this->units = $units;
        return $this;
    }

    /**
     * Decimal fields cannot be type-hinted as float
     * @link https://github.com/symfony/symfony/issues/32124
     */
    public function getPrecision(): ?string
    {
        return $this->precision;
    }

    public function setPrecision(?float $precision): Cavegrid
    {
        $this->precision = $precision;
        return $this;
    }

    /**
     * Decimal fields cannot be type-hinted as float
     * @link https://github.com/symfony/symfony/issues/32124
     */
    public function getAccuracy(): ?string
    {
        return $this->accuracy;
    }

    public function setAccuracy(?float $accuracy): Cavegrid
    {
        $this->accuracy = $accuracy;
        return $this;
    }

    public function getEdition(): ?string
    {
        return $this->edition;
    }

    public function setEdition(?string $edition): Cavegrid
    {
        $this->edition = $edition;
        return $this;
    }

    public function getDate(): ?DateTime
    {
        return $this->date;
    }

    public function setDate(?DateTime $date): Cavegrid
    {
        $this->date = $date;
        return $this;
    }

//    public function getGeodeticdatum(): ?string
//    {
//        return $this->geodeticdatum;
//    }

//    public function setGeodeticdatum(?string $geodeticdatum): Cavegrid
//    {
//        $this->geodeticdatum = $geodeticdatum;
//        return $this;
//    }

//    public function getMapgrid(): ?string
//    {
//        return $this->mapgrid;
//    }

//    public function setMapgrid(?string $mapgrid): Cavegrid
//    {
//        $this->mapgrid = $mapgrid;
//        return $this;
//    }
//
//    /**
//     * Decimal fields cannot be type-hinted as float
//     * @link https://github.com/symfony/symfony/issues/32124
//     */
//    public function getMapscale(): ?string
//    {
//        return $this->mapscale;
//    }

//    public function setMapscale(?float $mapscale): Cavegrid
//    {
//        $this->mapscale = $mapscale;
//        return $this;
//    }

//    public function getMapnumber(): ?string
//    {
//        return $this->mapnumber;
//    }

//    public function setMapnumber(?string $mapnumber): Cavegrid
//    {
//        $this->mapnumber = $mapnumber;
//        return $this;
//    }

//    public function getMapname(): ?string
//    {
//        return $this->mapname;
//    }

//    public function setMapname(?string $mapname): Cavegrid
//    {
//        $this->mapname = $mapname;
//        return $this;
//    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): Cavegrid
    {
        $this->comment = $comment;
        return $this;
    }
}
