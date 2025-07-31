<?php
namespace  App\Domain\Fielddefinition\Entity;
use Doctrine\Common\Collections\{Collection, Criteria};
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Fieldvaluecode. Numeric codes and their meanings in English
 *  for those fields whose data values are taken from a
 *  fixed vocabulary of terms
 * @see http://www.uisic.uis-speleo.org/exchange/atencode.html
 */
#[ORM\Table(name: 'fieldvaluecode')]
#[ORM\Entity]
class Fieldvaluecode
{
    /**
     * Code Id = Fieldvaluecode code [1-99999] + [dot] + Choice code[1-999].
     */
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    #[ORM\Column(name: 'id', type: 'string', length: 9)]
    private string $id;

    /**
     * Usually same as Fielddefinition code
     */
    #[ORM\Column(name: 'field', type: 'smallint')]
    #[Assert\Range(min: 1, max: 99999)]
    private ?int $field = null;

    /**
     * Code for this field
     */
    #[ORM\Column(name: 'code', type: 'string', length: 3)]
    private ?string $code = null;

    /**
     * Meaning in english
     */
    #[ORM\Column(name: 'value', type: 'string', length: 65)]
    private ?string $value = null;

    /**
     * Translations
     */
    #[ORM\OneToMany(mappedBy: 'id', targetEntity: Fieldvaluecodelang::class, cascade: ['persist', 'remove'], indexBy: 'locale')]
    private Collection $translations;

    public function getId(): string
    {
        return $this->id;
    }

    public function getField(): int
    {
        return $this->field;
    }

    public function setField(int $field): Fieldvaluecode
    {
        $this->field = $field;
        return $this;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): Fieldvaluecode
    {
        $this->code = $code;
        return $this;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function setValue(string $value): Fieldvaluecode
    {
        $this->value = $value;
        return $this;
    }

    public function __toString(): string {
        return sprintf('ID: %s, Field: %s, Code: %s, Value: %s', $this->id, $this->field, $this->code, $this->value);
    }

    public function getTranslations(): ?Collection
    {
        return $this->translations;
    }

    public function getTranslationByLocale($locale): ?string
    {
        $criteria = Criteria::create()->where(Criteria::expr()->eq("locale", $locale));
        return  $this->translations->matching($criteria)->first();
    }
}
