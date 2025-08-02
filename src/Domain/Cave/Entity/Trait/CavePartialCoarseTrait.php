<?php
namespace  App\Domain\Cave\Entity\Trait;
use App\Entity\Cave\Trait\Cave;
use Doctrine\ORM\Mapping as ORM;

trait CavePartialCoarseTrait
{
    /**
     * FD 21
     */
    #[ORM\Column(name: 'latitude_coarse', type: 'decimal', precision: 8, scale: 4, nullable: true)]
    private ?string $latitudecoarse;

    /**
     *  FD 22
     */
    #[ORM\Column(name: 'longitude_coarse', type: 'decimal', precision: 9, scale: 4, nullable: true)]
    private ?string $longitudecoarse;

    /**
     * FD 23
     */
    #[ORM\Column(name: 'map_sheet_name_coarse', type: 'string', length: 30, nullable: true)]
    private ?string $mapsheetnamecoarse;

    /**
     * FD 26
     */
    #[ORM\Column(name: 'grid_reference_coarse', type: 'string', length: 4, nullable: true)]
    private ?string $gridreferencecoarse;

    /**
     * FD 25
     */
    #[ORM\Column(name: 'grref_qualifier_coarse', type: 'string', length: 4, nullable: true)]
    private ?string $grrefqualifiercoarse;

    /**
     * FD 28
     */
    #[ORM\Column(name: 'altitude_coarse', type: 'decimal', precision: 7, scale: 1, nullable: true)]
    private ?string $altitudecoarse;


    /**
     * Decimal fields cannot be type-hinted as float
     * @link https://github.com/symfony/symfony/issues/32124
     * @return ?string
     */
    public function getLatitudecoarse(): ?string
    {
        return $this->latitudecoarse;
    }

    public function setLatitudecoarse(?float $latitudecoarse): Cave
    {
        $this->latitudecoarse = $latitudecoarse;
        return $this;
    }

    /**
     * Decimal fields cannot be type-hinted as float
     * @link https://github.com/symfony/symfony/issues/32124
     */
    public function getLongitudecoarse(): ?string
    {
        return $this->longitudecoarse;
    }

    public function setLongitudecoarse(?float $longitudecoarse): Cave
    {
        $this->longitudecoarse = $longitudecoarse;
        return $this;
    }

    public function getMapsheetnamecoarse(): ?string
    {
        return $this->mapsheetnamecoarse;
    }

    public function setMapsheetnamecoarse(?string $mapsheetnamecoarse): Cave
    {
        $this->mapsheetnamecoarse = $mapsheetnamecoarse;
        return $this;
    }

    public function getGridreferencecoarse(): ?string
    {
        return $this->gridreferencecoarse;
    }

    public function setGridreferencecoarse(?string $gridreferencecoarse): Cave
    {
        $this->gridreferencecoarse = $gridreferencecoarse;
        return $this;
    }

    public function getGrrefqualifiercoarse(): ?string
    {
        return $this->grrefqualifiercoarse;
    }

    public function setGrrefqualifiercoarse(?string $grrefqualifiercoarse): Cave
    {
        $this->grrefqualifiercoarse = $grrefqualifiercoarse;
        return $this;
    }

    /**
     * Decimal fields cannot be type-hinted as float
     * @link https://github.com/symfony/symfony/issues/32124
     */
    public function getAltitudecoarse(): ?string
    {
        return $this->altitudecoarse;
    }

    public function setAltitudecoarse(?float $altitudecoarse): Cave
    {
        $this->altitudecoarse = $altitudecoarse;
        return $this;
    }
 }