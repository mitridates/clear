<?php
namespace  App\Entity\FieldDefinition;
use App\Entity\CommonTrait\{CrupdatetimeTrait};
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'fielddefinition_lang')]
#[ORM\Index(columns: ['id'], name: 'field_definitioin_idx')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class Fielddefinitionlang
{
    use FielddefinitionTrait, CrupdatetimeTrait;

    /**
     * Fielddefinition id
     */
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Fielddefinition::class, inversedBy: 'translations')]
    #[ORM\JoinColumn(name: 'id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private Fielddefinition $id;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    #[ORM\Column(name: 'locale', type: 'string', nullable: false)]
    private string $locale;

    #[ORM\Column(name: 'review', type: 'boolean', nullable: false, options: ['default' => 0])]
    private bool $review = false;

    public function __construct(Fielddefinition $id)
    {
        $this->id = $id;
    }
 
    public function getLocale(): string
    {
        return $this->locale;
    }

    public function setLocale(string $locale): Fielddefinitionlang
    {
        $this->locale = $locale;
        return $this;
    }

    public function getFieldDefinition(): ?Fielddefinition
    {
        return $this->id;
    }

    public function getReview(): bool
    {
        return $this->review;
    }

    public function setReview(bool $review): Fielddefinitionlang
    {
        $this->review = $review;
        return $this;
    }
}
