<?php
namespace  App\Domain\Fielddefinition\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Fieldvaluecode translations.
 */
#[ORM\Table(name: 'fieldvaluecode_lang')]
#[ORM\Entity]
class Fieldvaluecodelang
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Fieldvaluecode::class, inversedBy: 'translations')]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    #[ORM\JoinColumn(name: 'id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private Fieldvaluecode $id;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    #[ORM\Column(name: 'locale', type: 'string')]
    private string $locale;

    #[ORM\Column(name: 'value', type: 'string', length: 65 , nullable: false)]
    private string $value;


    public function __construct(Fieldvaluecode $f)
    {
        $this->id = $f;
    }

    public function getId(): Fieldvaluecode
    {
        return $this->id;
    }
    public function getFieldValueCode(): ?Fieldvaluecode
    {
        return $this->id;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function setLocale(string $locale): Fieldvaluecodelang
    {
        $this->locale = $locale;
        return $this;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function setValue(string $value): Fieldvaluecodelang
    {
        $this->value = $value;
        return $this;
    }
}
