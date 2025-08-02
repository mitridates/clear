<?php
namespace  App\Domain\Citation\Entity\Trait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Registered id's
 */
trait CitationRegisteredIdentifiersTrait
{
use CitationBookArticleBaseTrait;

    /**
     * ISBN . FD 608
     */
    #[Assert\Length(min: 10, max: 10, exactMessage: 'cave.validator.exact.length')]
    #[ORM\Column(name: 'isbn', type: 'string', length: 10, nullable: true)]
    private ?string $isbn;
    /**
     * ISSN . FD 320
     */
    #[Assert\Length(min: 9, max: 9, exactMessage: 'cave.validator.exact.length')]
    #[ORM\Column(name: 'issn', type: 'string', length: 10, nullable: true)]
    private ?string $issn;

    /**
     * No FD
     */
    #[Assert\Length(max: 200)]
    #[ORM\Column(name: 'copyright', type: 'string', length: 200, nullable: true)]
    private ?string $copyright=null;

    public function getIsbn(): ?string
    {
        return $this->isbn;
    }

    public function setIsbn(?string $isbn): self
    {
        $this->isbn = $isbn;
        return $this;
    }
    public function getIssn(): ?string
    {
        return $this->issn;
    }

    public function setIssn(?string $issn): self
    {
        $this->issn = $issn;
        return $this;
    }
    public function getCopyright(): ?string
    {
        return $this->copyright;
    }

    public function setCopyright(?string $copyright): self
    {
        $this->copyright = $copyright;
        return $this;
    }

}

