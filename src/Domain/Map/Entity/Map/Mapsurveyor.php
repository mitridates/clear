<?php
namespace  App\Domain\Map\Entity\Map;
use App\Domain\Map\Entity\Map\Model\MapManyToOneInterface;
use App\Domain\Map\Entity\Map\Trait\MapManyToOneTrait;
use App\Domain\Person\Entity\Person;
use CrupdatetimeTrait;
use Doctrine\ORM\Mapping as ORM;
use SequenceTrait;

/**
 * Surveyors (PL0586)
 */
#[ORM\Table(name: 'map_surveyor')]
#[ORM\Index(columns: ['map'], name: 'map_idx')]
#[ORM\Index(columns: ['map_surveyor_id'], name: 'map_person_surveyor_idx')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class Mapsurveyor implements MapManyToOneInterface
{
    use SequenceTrait, CrupdatetimeTrait, MapManyToOneTrait;

    /**
     * FD 195
     */
     #[ORM\ManyToOne(targetEntity:  Map::class, inversedBy: 'mapsurveyor')]
     #[ORM\JoinColumn(name: 'map', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
     private Map $map;

    /**
     * Map surveyor. FD 584
     */
    #[ORM\Column(name: 'map_surveyor', type: 'string', length: 25, nullable: true)]
    private ?string $surveyor = null;

    /**
     * Map surveyor. FD 586
     */
    #[ORM\ManyToOne(targetEntity: Person::class)]
    #[ORM\JoinColumn(name: 'map_surveyor_id', referencedColumnName: 'id', nullable: true)]
    private ?Person $surveyorid = null;

    public function setSurveyorid(?Person $surveyorid): Mapsurveyor
    {
        $this->surveyorid = $surveyorid;
        return $this;
    }

    public function getSurveyorid(): ?Person
    {
        return $this->surveyorid;
    }

    public function setSurveyor(?string $surveyor): Mapsurveyor
    {
        $this->surveyor = $surveyor;
        return $this;
    }

    public function getSurveyor(): ?string
    {
        return $this->surveyor;
    }
}

