<?php
namespace  App\Domain\Fielddefinition\Entity;
use App\Infrastructure\Doctrine\Trait\CrupdatetimeTrait;
use Doctrine\Common\Collections\{Collection};
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Fielddefinition
 */
#[ORM\Table(name: 'fielddefinition')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class Fielddefinition
{
    use FielddefinitionTrait;
    use CrupdatetimeTrait;


    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    #[Assert\Range(min: 1, max: 99999)]
    #[ORM\Column(name: 'id', type: 'integer', unique: true, nullable: false)]
    private int $id;

    
    #[ORM\Column(name: 'entity', type: 'string', length: 2, nullable: true)]
    private ?string $entity = null;

    
    #[ORM\Column(name: 'datatype', type: 'string', length: 1, nullable: true)]
    private ?string $datatype = null;

    
    #[ORM\Column(name: 'maxlength', type: 'string', length: 4, nullable: true)]
    private ?string $maxlength = null;

    
    #[ORM\Column(name: 'coding', type: 'string', length: 1, nullable: true)]
    private ?string $coding = null;

    
    #[ORM\Column(name: 'singlemultivalued', type: 'string', length: 1, nullable: true)]
    private ?string $singlemultivalued = null;

    /**
     * translations
     */
    #[ORM\OneToMany(mappedBy: 'id', targetEntity: Fielddefinitionlang::class, cascade: ['persist', 'remove'], indexBy: 'locale')]
    private Collection $translations;

    /**
     * Codes for this field in Fieldvaluecode
     */
    #[Assert\Range(min: 1, max: 99999)]
    #[ORM\Column(name: 'valuecode', type: 'smallint', nullable: true)]
    private ?int $valuecode = null ;

    public function getEntity(): ?string
    {
        return $this->entity;
    }

    public function setEntity(?string $entity): Fielddefinition
    {
        $this->entity = $entity;
        return $this;
    }

    public function getDatatype(): ?string
    {
        return $this->datatype;
    }

    public function setDatatype(?string $datatype): Fielddefinition
    {
        $this->datatype = strtoupper($datatype);
        return $this;
    }

    public function getMaxlength(): ?string
    {
        return $this->maxlength;
    }

    public function setMaxlength(?string $maxlength): Fielddefinition
    {
        $this->maxlength = $maxlength;
        return $this;
    }

    public function getCoding(): ?string
    {
        return $this->coding;
    }

    public function setCoding(?string $coding): Fielddefinition
    {
        $this->coding = strtoupper($coding);
        return $this;
    }

    public function getSinglemultivalued(): ?string
    {
        return $this->singlemultivalued;
    }

    public function setSinglemultivalued(?string $singlemultivalued): Fielddefinition
    {
        $this->singlemultivalued = strtoupper($singlemultivalued);
        return $this;
    }

    public function getValuecode(): ?string
    {
        return $this->valuecode;
    }

    public function setValuecode(?int $valuecode): Fielddefinition
    {
        $this->valuecode = $valuecode;
        return $this;
    }

    public function getTranslations(): Collection
    {
        return $this->translations;
    }
}
