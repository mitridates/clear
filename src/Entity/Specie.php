<?php
namespace  App\Entity;
use App\Entity\CommonTrait\CrupdatetimeTrait;
use App\Entity\CommonTrait\HiddenTrait;
use App\Utils\Doctrine\Orm\Id\CavernIdGenerator;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

;

/**
 * Specie SP0000
 */
#[ORM\Table(name: 'specie')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class Specie
{
    use CrupdatetimeTrait, HiddenTrait;

    /**
     * Specie ID FD 364.
     */
    #[ORM\Column(name: 'id', type: 'string', length: 17, nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: CavernIdGenerator::class)]
    private string $id;

    /**
     * Species phylum. FD 605.
     */
    #[ORM\Column(name: 'phylum', type: 'string', length: 30, nullable: true)]
    private ?string $phylum = null;

    /**
     * Genus. Code 281
     */
    #[ORM\Column(name: 'genus', type: 'string', length: 30, nullable: true)]
    private ?string $genus = null;

    /**
     * Specie family. FD 602.
     */
    #[ORM\Column(name: 'family', type: 'string', length: 30, nullable: true)]
    private ?string $family = null;    

    /**
     * Specie order. FD 603
     */
    #[ORM\Column(name: 'orden', type: 'string', length: 30, nullable: true)]
    private ?string $orden = null;    

    /**
     * Specie class. FD 603.
     */
    #[Assert\Length(max: 30, maxMessage: 'cave.validator.max.length')]
    #[ORM\Column(name: 'class', type: 'string', length: 30, nullable: true)]
    private ?string $class = null;      

    /**
     * Specie name. FD 282.
     */
    #[ORM\Column(name: 'name', type: 'string', length: 30, nullable: true)]
    private ?string $name = null;

    /**
     * Specie common name. FD 606.
     */
    #[ORM\Column(name: 'specie_common_name', type: 'string', length: 30, nullable: true)]
    private ?string $commonname = null;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getPhylum(): ?string
    {
        return $this->phylum;
    }

    public function setPhylum(?string $phylum): Specie
    {
        $this->phylum = $phylum;
        return $this;
    }

    public function getGenus(): ?string
    {
        return $this->genus;
    }

    public function setGenus(?string $genus): Specie
    {
        $this->genus = $genus;
        return $this;
    }

    public function getFamily(): ?string
    {
        return $this->family;
    }

    public function setFamily(?string $family): Specie
    {
        $this->family = $family;
        return $this;
    }

    public function getOrden(): ?string
    {
        return $this->orden;
    }

    public function setOrden(?string $orden): Specie
    {
        $this->orden = $orden;
        return $this;
    }

    public function getClass(): ?string
    {
        return $this->class;
    }

    public function setClass(?string $class): Specie
    {
        $this->class = $class;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): Specie
    {
        $this->name = $name;
        return $this;
    }

    public function getCommonname(): ?string
    {
        return $this->commonname;
    }

    public function setCommonname(?string $commonname): Specie
    {
        $this->commonname = $commonname;
        return $this;
    }
}
