<?php
namespace  App\Domain\Cave\Entity\Trait;
use App\Domain\Fielddefinition\Entity\Fieldvaluecode;
use App\Domain\Cave\Entity\Cave;
use Doctrine\ORM\Mapping as ORM;

trait CavePartialDimensionTrait
{
    /**
     * FD 56
     */
    #[ORM\Column(name: 'length', type: 'decimal', precision: 10, scale: 1, nullable: true)]
    private ?string $length;

    /**
     * FD 57
     */
    #[ORM\ManyToOne(targetEntity: Fieldvaluecode::class)]
    #[ORM\JoinColumn(name: 'length_method', referencedColumnName: 'id')]
    private ?Fieldvaluecode $lengthmethod;

    /**
     * FD 58
     */
    #[ORM\Column(name: 'length_accuracy', type: 'decimal', precision: 10, scale: 2, nullable: true)]
    private ?string $lengthaccuracy;

    /**
     * FD 59
     */
    #[ORM\Column(name: 'extent_below_entrance', type: 'decimal', precision: 8, scale: 2, nullable: true)]
    private ?string $extentbelowentrance;

    /**
     * FD 61
     */
    #[ORM\ManyToOne(targetEntity: Fieldvaluecode::class)]
    #[ORM\JoinColumn(name: 'vertical_method', referencedColumnName: 'id', nullable: true)]
    private ?Fieldvaluecode $verticalmethod;

    /**
     * FD 62
     */
    #[ORM\Column(name: 'vertical_accuracy', type: 'decimal', precision: 7, scale: 2, nullable: true)]
    private ?string $verticalaccuracy;

    /**
     * FD 63
     */
    #[ORM\Column(name: 'length_of_largest_chamber', type: 'decimal', precision: 8, scale: 2, nullable: true)]
    private ?string $lengthlargestchamber;

    /**
     * FD 64
     */
    #[ORM\Column(name: 'width_of_largest_chamber', type: 'decimal', precision: 8, scale: 2, nullable: true)]
    private ?string $widthlargestchamber;

    /**
     * FD 65
     */
    #[ORM\Column(name: 'height_of_largest_chamber', type: 'decimal', precision: 8, scale: 2, nullable: true)]
    private ?string $heighlargestchamber;

    /**
     * FD 67
     */
    #[ORM\Column(name: 'extent_length', type: 'decimal', precision: 8, scale: 1, nullable: true)]
    private ?string $extentlength;

    /**
     * FD 68
     */
    #[ORM\Column(name: 'extent_width', type: 'decimal', precision: 8, scale: 1, nullable: true)]
    private ?string $extentwidth;

    /**
     * FD 511
     */
    #[ORM\Column(name: 'vertical_extent', type: 'decimal', precision: 7, scale: 2, nullable: true)]
    private ?string $verticalextent;

    /**
     * FD 60
     */
    #[ORM\Column(name: 'extent_above_entrance', type: 'decimal', precision: 8, scale: 2, nullable: true)]
    private ?string $extentaboveentrance;

    /**
     * FD 297
     */
    #[ORM\ManyToOne(targetEntity: Fieldvaluecode::class)]
    #[ORM\JoinColumn(name: 'length_category', referencedColumnName: 'id', nullable: true)]
    private ?Fieldvaluecode $lengthcategory;

    /**
     * FD 527
     */
    #[ORM\ManyToOne(targetEntity: Fieldvaluecode::class)]
    #[ORM\JoinColumn(name: 'depth_category', referencedColumnName: 'id', nullable: true)]
    private ?Fieldvaluecode $depthcategory;

    /**
     * Decimal fields cannot be type-hinted as float
     * @link https://github.com/symfony/symfony/issues/32124
     * @return ?string
     */
    public function getLength(): ?string
    {
        return $this->length;
    }

    public function setLength(?float $length): Cave
    {
        $this->length = $length;
        return $this;
    }

    public function getLengthmethod(): ?Fieldvaluecode
    {
        return $this->lengthmethod;
    }

    public function setLengthmethod(?Fieldvaluecode $lengthmethod): Cave
    {
        $this->lengthmethod = $lengthmethod;
        return $this;
    }

    public function getLengthaccuracy(): ?string
    {
        return $this->lengthaccuracy;
    }

    public function setLengthaccuracy(?string $lengthaccuracy): Cave
    {
        $this->lengthaccuracy = $lengthaccuracy;
        return $this;
    }

    /**
     * Decimal fields cannot be type-hinted as float
     * @link https://github.com/symfony/symfony/issues/32124
     */
    public function getExtentbelowentrance(): ?string
    {
        return $this->extentbelowentrance;
    }

    public function setExtentbelowentrance(?float $extentbelowentrance): Cave
    {
        $this->extentbelowentrance = $extentbelowentrance;
        return $this;
    }

    public function getVerticalmethod(): ?Fieldvaluecode
    {
        return $this->verticalmethod;
    }

    public function setVerticalmethod(?Fieldvaluecode $verticalmethod): Cave
    {
        $this->verticalmethod = $verticalmethod;
        return $this;
    }

    public function getVerticalaccuracy(): ?string
    {
        return $this->verticalaccuracy;
    }
    
    /**
     * Decimal fields cannot be type-hinted as float
     * @link https://github.com/symfony/symfony/issues/32124
     */
    public function setVerticalaccuracy(?string $verticalaccuracy): Cave
    {
        $this->verticalaccuracy = $verticalaccuracy;
        return $this;
    }
    /**
     * Decimal fields cannot be type-hinted as float
     * @link https://github.com/symfony/symfony/issues/32124
     */
    public function getLengthlargestchamber(): ?string
    {
        return $this->lengthlargestchamber;
    }

    public function setLengthlargestchamber(?float $lengthlargestchamber): Cave
    {
        $this->lengthlargestchamber = $lengthlargestchamber;
        return $this;
    }

    /**
     * Decimal fields cannot be type-hinted as float
     * @link https://github.com/symfony/symfony/issues/32124
     */
    public function getWidthlargestchamber(): ?string
    {
        return $this->widthlargestchamber;
    }

    public function setWidthlargestchamber(?float $widthlargestchamber): Cave
    {
        $this->widthlargestchamber = $widthlargestchamber;
        return $this;
    }

    /**
     * Decimal fields cannot be type-hinted as float
     * @link https://github.com/symfony/symfony/issues/32124
     */
    public function getHeighlargestchamber(): ?string
    {
        return $this->heighlargestchamber;
    }

    public function setHeighlargestchamber(?float $heighlargestchamber): Cave
    {
        $this->heighlargestchamber = $heighlargestchamber;
        return $this;
    }
    /**
     * Decimal fields cannot be type-hinted as float
     * @link https://github.com/symfony/symfony/issues/32124
     */
    public function getExtentlength(): ?string
    {
        return $this->extentlength;
    }

    public function setExtentlength(?float $extentlength): Cave
    {
        $this->extentlength = $extentlength;
        return $this;
    }

    /**
     * Decimal fields cannot be type-hinted as float
     * @link https://github.com/symfony/symfony/issues/32124
     */
    public function getExtentwidth(): ?string
    {
        return $this->extentwidth;
    }

    public function setExtentwidth(?float $extentwidth): Cave
    {
        $this->extentwidth = $extentwidth;
        return $this;
    }

    /**
     * Decimal fields cannot be type-hinted as float
     * @link https://github.com/symfony/symfony/issues/32124
     */
    public function getVerticalextent(): ?string
    {
        return $this->verticalextent;
    }

    public function setVerticalextent(?float $verticalextent): Cave
    {
        $this->verticalextent = $verticalextent;
        return $this;
    }

    /**
     * Decimal fields cannot be type-hinted as float
     * @link https://github.com/symfony/symfony/issues/32124
     */
    public function getExtentaboveentrance(): ?string
    {
        return $this->extentaboveentrance;
    }

    public function setExtentaboveentrance(?float $extentaboveentrance): Cave
    {
        $this->extentaboveentrance = $extentaboveentrance;
        return $this;
    }

    public function getLengthcategory(): ?Fieldvaluecode
    {
        return $this->lengthcategory;
    }

    public function setLengthcategory(?Fieldvaluecode $lengthcategory): Cave
    {
        $this->lengthcategory = $lengthcategory;
        return $this;
    }

    public function getDepthcategory(): ?Fieldvaluecode
    {
        return $this->depthcategory;
    }

    public function setDepthcategory(?Fieldvaluecode $depthcategory): Cave
    {
        $this->depthcategory = $depthcategory;
        return $this;
    }
}