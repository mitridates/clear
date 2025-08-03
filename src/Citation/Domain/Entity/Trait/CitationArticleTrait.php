<?php
namespace  App\Citation\Domain\Entity\Trait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Citation article trait
 */
trait CitationArticleTrait
{
    use CitationBookArticleBaseTrait;

    /**
     * Publication name. Code 311
     */
    #[ORM\Column(name: 'journalname', type: 'string', length: 132, nullable: true)]
    #[Assert\Length(max: 132, maxMessage: 'cave.validator.max.length')]
    private ?string $journalname;

    /**
     * Ref - issue number. FD 313
     */
    #[Assert\Length(max: 4, maxMessage: 'cave.validator.max.length')]
    #[ORM\Column(name: 'issue_number', type: 'string', length: 4, nullable: true)]
    private? string $issuenumber;

    /**
     * ISSN . FD 320
     */
    #[Assert\Length(min: 9, max: 9, exactMessage: 'cave.validator.exact.length')]
    #[ORM\Column(name: 'issn', type: 'string', length: 10, nullable: true)]
    private ?string $issn;

    public function getJournalname(): ?string
    {
        return $this->journalname;
    }

    public function setJournalname(?string $journalname): self
    {
        $this->journalname = $journalname;
        return $this;
    }

    public function getIssuenumber(): ?string
    {
        return $this->issuenumber;
    }

    public function setIssuenumber(?string $issuenumber): self
    {
        $this->issuenumber = $issuenumber;
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

}

