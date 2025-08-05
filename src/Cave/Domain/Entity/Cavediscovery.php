<?php
namespace  App\Cave\Domain\Entity;
use App\Cave\Domain\Entity\Trait\CaveManyToOneTrait;
use App\Fielddefinition\Domain\Entity\Fieldvaluecode;
use App\Organisation\Domain\Entity\Organisation;
use App\Person\Domain\Entity\Person;
use App\Shared\Doctrine\Trait\CrupdatetimeTrait;
use App\Shared\Doctrine\Trait\SequenceTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * CA0030 Cave discovery 0:n
 */
#[ORM\Table(name: 'cave_discovery')]
#[ORM\Index(columns: ['cave'], name: 'cave_idx')]
#[ORM\Index(columns: ['discoverer_type'], name: 'fieldvaluecode_discoverer_type_idx')]
#[ORM\Index(columns: ['discoverer_person_id'], name: 'discoverer_person_id_idx')]
#[ORM\Index(columns: ['discoverer_org_id'], name: 'organisation_discoverer_org_id_idx')]
#[ORM\Index(columns: ['discoverer_date_qualifier'], name: 'fieldvaluecode_discoverer_date_qualifier_idx')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class Cavediscovery
{
    use SequenceTrait, CrupdatetimeTrait, CaveManyToOneTrait;

    /**
     * FD 227
     */
    #[ORM\ManyToOne(targetEntity: Cave::class, inversedBy: 'cavediscovery')]
    #[ORM\JoinColumn(name: 'cave', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private Cave $cave;


    /**
     * FD 30
     */
    #[ORM\ManyToOne(targetEntity: Fieldvaluecode::class)]
    #[ORM\JoinColumn(name: 'discoverer_type', referencedColumnName: 'id', nullable: true)]
    private ?Fieldvaluecode $type = null;

    /**
     * FD FD 419
     */
    #[ORM\ManyToOne(targetEntity: Person::class)]
    #[ORM\JoinColumn(name: 'discoverer_person_id', referencedColumnName: 'id', nullable: true)]
    private ?Person $person = null;    

    /**
     * Discoverer first name. FD 523
     */
    #[ORM\Column(name: 'discoverer_firstname', type: 'string', length: 20, nullable: true)]
    private ?string $firstname = null;

    /**
     * Discoverer surname. FD 522
     */
    #[ORM\Column(name: 'discoverer_surname', type: 'string', length: 30, nullable: true)]
    private ?string $surname = null;    

    /**
     * Discoverer organisation. FD 420
     */
    #[ORM\ManyToOne(targetEntity: Organisation::class)]
    #[ORM\JoinColumn(name: 'discoverer_org_id', referencedColumnName: 'id', nullable: true)]
    private ?Organisation $organisation = null;

    /**
     * Discoverer organisation initials. FD 524
     */
    #[ORM\Column(name: 'discoverer_org_initials', type: 'string', length: 8, nullable: true)]
    private ?string $organisationinitials = null;  

    /**
     * Discoverer name. FD 31
     */
    #[ORM\Column(name: 'discoverer_name', type: 'string', length: 45, nullable: true)]
    private ?string $name = null;

    /**
     * Discoverer month. FD 33
     */
    #[Assert\Range(min: 1, max: 12)]
    #[ORM\Column(name: 'discoverer_month', type: 'smallint', length: 2, nullable: true, options: ['unsigned' => true, 'fixed' => true])]
    private ?int $month = null;    

    /**
     * Discoverer day. FD 32
     */
    #[Assert\Range(min: 1, max: 31)]
    #[ORM\Column(name: 'discoverer_day', type: 'smallint', length: 2, nullable: true, options: ['unsigned' => true, 'fixed' => true])]
    private ?int $day = null;      

    /**
     * Discoverer year. FD 34
     */
    #[Assert\Length(min: 4, max: 4, exactMessage: 'cave.validator.exact.length')]
    #[ORM\Column(name: 'discoverer_year', type: 'smallint', length: 4, nullable: true, options: ['unsigned' => true, 'fixed' => true])]
    private ?int $year = null;        

    /**
     * Discoverer date qualifier. FD 477
     */
    #[ORM\ManyToOne(targetEntity: Fieldvaluecode::class)]
    #[ORM\JoinColumn(name: 'discoverer_date_qualifier', referencedColumnName: 'id', nullable: true)]
    private ?Fieldvaluecode $datequalifier = null;

    public function getType(): ?Fieldvaluecode
    {
        return $this->type;
    }
 
    public function setType(Fieldvaluecode $type): Cavediscovery
    {
        $this->type = $type;
        return $this;
    }

    public function getPerson(): ?Person
    {
        return $this->person;
    }
 
    public function setPerson(?Person $person): Cavediscovery
    {
        $this->person = $person;
        return $this;
    }
 
    public function getFirstname(): ?string
    {
        return $this->firstname;
    }
 
    public function setFirstname(?string $firstname): Cavediscovery
    {
        $this->firstname = $firstname;
        return $this;
    }
 
    public function getSurname(): ?string
    {
        return $this->surname;
    }
 
    public function setSurname(?string $surname): Cavediscovery
    {
        $this->surname = $surname;
        return $this;
    }
 
    public function getOrganisation(): ?Organisation
    {
        return $this->organisation;
    }
 
    public function setOrganisation(?Organisation $organisation): Cavediscovery
    {
        $this->organisation = $organisation;
        return $this;
    }
 
    public function getOrganisationinitials(): ?string
    {
        return $this->organisationinitials;
    }
 
    public function setOrganisationinitials(?string $organisationinitials): Cavediscovery
    {
        $this->organisationinitials = $organisationinitials;
        return $this;
    }
 
    public function getName(): ?string
    {
        return $this->name;
    }
 
    public function setName(?string $name): Cavediscovery
    {
        $this->name = $name;
        return $this;
    }
 
    public function getMonth(): ?int
    {
        return $this->month;
    }
 
    public function setMonth(?int $month): Cavediscovery
    {
        $this->month = $month;
        return $this;
    }
 
    public function getDay(): ?int
    {
        return $this->day;
    }
 
    public function setDay(?int $day): Cavediscovery
    {
        $this->day = $day;
        return $this;
    }
 
    public function getYear(): ?int
    {
        return $this->year;
    }
 
    public function setYear(?int $year): Cavediscovery
    {
        $this->year = $year;
        return $this;
    }

    /**
     * @return ?Fieldvaluecode
     */
    public function getDatequalifier(): ?Fieldvaluecode
    {
        return $this->datequalifier;
    }
 
    public function setDatequalifier(?Fieldvaluecode $datequalifier): Cavediscovery
    {
        $this->datequalifier = $datequalifier;
        return $this;
    }
}

