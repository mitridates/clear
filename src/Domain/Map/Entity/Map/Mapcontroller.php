<?php
namespace  App\Domain\Map\Entity\Map;
use App\Domain\Map\Entity\Map\Model\MapOneToOneInterface;
use App\Domain\Map\Entity\Map\Trait\MapOneToOneTrait;
use App\Domain\Organisation\Entity\Organisation;
use App\Domain\Person\Entity\Person;
use  App\Infrastructure\Doctrine\Trait\CrupdatetimeTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * PL0406 Current map controller
 */
#[ORM\Table(name: 'map_controller')]
#[ORM\Index(columns: ['map_controller_org_ID'], name: 'organisation_controller_idx')]
#[ORM\Index(columns: ['map_controller_person_ID'], name: 'person_controller_idx')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class Mapcontroller implements MapOneToOneInterface
{
    use MapOneToOneTrait, CrupdatetimeTrait;

    /**
      * FD 195
      */
     #[ORM\Id]
     #[ORM\GeneratedValue(strategy: 'NONE')]
     #[ORM\OneToOne(targetEntity: Map::class)]
     #[ORM\JoinColumn(name: 'map', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
     private Map $map;

    /**
     * Map controller org ID. FD 406
     */
    #[ORM\ManyToOne(targetEntity: Organisation::class)]
    #[ORM\JoinColumn(name: 'map_controller_org_ID', referencedColumnName: 'id', nullable: true)]
    private ?Organisation $organisation = null;     

    /**
     * Map controller person ID. FD 407
     */
    #[ORM\ManyToOne(targetEntity: Person::class)]
    #[ORM\JoinColumn(name: 'map_controller_person_ID', referencedColumnName: 'id', nullable: true)]
    private ?Person $person = null;   

    /**
     * Map controller if no ID. FD 210
     */
    #[ORM\Column(name: 'map_controller_if_no_ID', type: 'string', length: 70, nullable: true)]
    private ?string $controllerifnoid = null;

    /**
     * FD 408
     */
    #[ORM\Column(name: 'map_controller_comment', type: 'string', length: 70, nullable: true)]
    private ?string $comment = null;        

    public function setOrganisation(?Organisation $organisation): Mapcontroller
    {
        $this->organisation = $organisation;
        return $this;
    }

    public function getOrganisation(): ?Organisation
    {
        return $this->organisation;
    }

    public function setPerson(?Person $person): Mapcontroller
    {
        $this->person = $person;
        return $this;
    }

    public function getPerson(): ?Person
    {
        return $this->person;
    }

    public function setControllerifnoid(?string $controllerifnoid): Mapcontroller
    {
        $this->controllerifnoid = $controllerifnoid;
        return $this;
    }

    public function getControllerifnoid(): ?string
    {
        return $this->controllerifnoid;
    }

    public function setComment(?string $comment): Mapcontroller
    {
        $this->comment = $comment;
        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }
}

