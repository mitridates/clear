<?php
namespace  App\Domain\Fielddefinition\Entity;
use Doctrine\ORM\Mapping as ORM;

trait FielddefinitionTrait
{

    /**
     * @var string
     */
    #[ORM\Column(name: 'name', type: 'string', length: 30, nullable: false)]
    private string $name;
    /**
     * abbreviation
     */
    #[ORM\Column(name: 'abbreviation', type: 'string', length: 30, nullable: true)]
    private ?string $abbreviation;

    #[ORM\Column(name: 'definition', type: 'text', nullable: true)]
    private ?string $definition;

    #[ORM\Column(name: 'example', type: 'text', nullable: true)]
    private ?string $example;

    #[ORM\Column(name: 'comment', type: 'text', nullable: true)]
    private ?string $comment;

    #[ORM\Column(name: 'uso', type: 'text', nullable: true)]
    private ?string $uso;

    /**
     * If used in translations return relationship, else int
     */
    public function getId(): int|Fielddefinition
    {
        return $this->id;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    function getAbbreviation(): ?string
    {
        return $this->abbreviation;
    }

    function setAbbreviation($abbreviation): self
    {
        $this->abbreviation = $abbreviation;
        return $this;
    }

    public function setDefinition($definition): self
    {
        $this->definition = $definition;
        return $this;
    }

    public function getDefinition(): ?string
    {
        return $this->definition;
    }

    public function setExample($example): self
    {
        $this->example = $example;
        return $this;
    }

    public function getExample(): ?string
    {
        return $this->example;
    }

    public function setComment($comment): self
    {
        $this->comment = $comment;
        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setUso($uso): self
    {
        $this->uso = $uso;
        return $this;
    }

    public function getUso(): ?string
    {
        return $this->uso;
    }
}

