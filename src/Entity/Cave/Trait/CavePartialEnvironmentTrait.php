<?php
namespace  App\Entity\Cave\Trait;
use App\Domain\Fielddefinition\Entity\Fieldvaluecode;
use App\Entity\Cave\Cave;
use Doctrine\ORM\Mapping as ORM;

trait CavePartialEnvironmentTrait
{

    /**
     * FD 2
     */
    #[ORM\ManyToOne(targetEntity: Fieldvaluecode::class)]
    #[ORM\JoinColumn(name: 'submersion', referencedColumnName: 'id', nullable: true)]
    private ?Fieldvaluecode $submersion;

    /**
     * FD 3
     */
    #[ORM\ManyToOne(targetEntity: Fieldvaluecode::class)]
    #[ORM\JoinColumn(name: 'flow_presence', referencedColumnName: 'id', nullable: true)]
    private ?Fieldvaluecode $flowpresence;

    /**
     * FD 4
     */
    #[ORM\ManyToOne(targetEntity: Fieldvaluecode::class)]
    #[ORM\JoinColumn(name: 'flow_direction', referencedColumnName: 'id', nullable: true)]
    private ?Fieldvaluecode $flowdirection;

    /**
     * FD 5
     */
    #[ORM\ManyToOne(targetEntity: Fieldvaluecode::class)]
    #[ORM\JoinColumn(name: 'internal_flow', referencedColumnName: 'id', nullable: true)]
    private ?Fieldvaluecode $internalflow;

    /**
     * FD 6
     */
    #[ORM\ManyToOne(targetEntity: Fieldvaluecode::class)]
    #[ORM\JoinColumn(name: 'internal_water', referencedColumnName: 'id', nullable: true)]
    private ?Fieldvaluecode $internalwater;

    /**
     * FD 13
     */
    #[ORM\Column(name: 'minimum_temperature', type: 'decimal', precision: 5, scale: 1, nullable: true)]
    private ?string $minimumtemperature;

    /**
     * Maximum temperature. FD 14
     */
    #[ORM\Column(name: 'maximum_temperature', type: 'decimal', precision: 5, scale: 1, nullable: true)]
    private ?string $maximumtemperature;

    /**
     * FD 15
     */
    #[ORM\Column(name: 'minimum_humidity', type: 'decimal', precision: 6, scale: 1, nullable: true)]
    private ?string $minimumhumidity;

    /**
     * FD 16
     */
    #[ORM\Column(name: 'maximum_humidity', type: 'decimal', precision: 6, scale: 1, nullable: true)]
    private ?string $maximumhumidity;

    /**
     * FD 17
     */
    #[ORM\ManyToOne(targetEntity: Fieldvaluecode::class)]
    #[ORM\JoinColumn(name: 'moisture_level', referencedColumnName: 'id', nullable: true)]
    private ?Fieldvaluecode $moisturelevel;

    public function getSubmersion(): ?Fieldvaluecode
    {
        return $this->submersion;
    }

    public function setSubmersion(?Fieldvaluecode $submersion): Cave
    {
        $this->submersion = $submersion;
        return $this;
    }

    public function getFlowpresence(): ?Fieldvaluecode
    {
        return $this->flowpresence;
    }

    public function setFlowpresence(?Fieldvaluecode $flowpresence): Cave
    {
        $this->flowpresence = $flowpresence;
        return $this;
    }

    public function getFlowdirection(): ?Fieldvaluecode
    {
        return $this->flowdirection;
    }

    public function setFlowdirection(?Fieldvaluecode $flowdirection): Cave
    {
        $this->flowdirection = $flowdirection;
        return $this;
    }

    public function getInternalflow(): ?Fieldvaluecode
    {
        return $this->internalflow;
    }

    public function setInternalflow(?Fieldvaluecode $internalflow): Cave
    {
        $this->internalflow = $internalflow;
        return $this;
    }

    public function getInternalwater(): ?Fieldvaluecode
    {
        return $this->internalwater;
    }

    public function setInternalwater(?Fieldvaluecode $internalwater): Cave
    {
        $this->internalwater = $internalwater;
        return $this;
    }

    public function getMinimumtemperature(): ?string
    {
        return $this->minimumtemperature;
    }

    public function setMinimumtemperature(?float $minimumtemperature): Cave
    {
        $this->minimumtemperature = $minimumtemperature;
        return $this;
    }

    public function getMaximumtemperature(): ?string
    {
        return $this->maximumtemperature;
    }

    public function setMaximumtemperature(?float $maximumtemperature): Cave
    {
        $this->maximumtemperature = $maximumtemperature;
        return $this;
    }

    public function getMinimumhumidity(): ?string
    {
        return $this->minimumhumidity;
    }

    public function setMinimumhumidity(?float $minimumhumidity): Cave
    {
        $this->minimumhumidity = $minimumhumidity;
        return $this;
    }

    public function getMaximumhumidity(): ?string
    {
        return $this->maximumhumidity;
    }

    public function setMaximumhumidity(?float $maximumhumidity): Cave
    {
        $this->maximumhumidity = $maximumhumidity;
        return $this;
    }

    public function getMoisturelevel(): ?Fieldvaluecode
    {
        return $this->moisturelevel;
    }

    public function setMoisturelevel(?Fieldvaluecode $moisturelevel): Cave
    {
        $this->moisturelevel = $moisturelevel;
        return $this;
    }
}