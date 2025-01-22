<?php
namespace  App\Entity\Citation\Trait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Citation book & article base trait
 */
trait CitationBookArticleBaseTrait
{
    /**
     * Ref - volume number. FD 312
     */
    #[Assert\Length(max: 7, maxMessage: 'cave.validator.max.length')]
    #[ORM\Column(name: 'volume_number', type: 'string', length: 7, nullable: true)]
    private ?string $volumenumber;


    /**
     * Page range or quantity. FD 315
     */
    #[Assert\Length(max: 13, maxMessage: 'cave.validator.max.length')]
    #[ORM\Column(name: 'page_range_or_quantity', type: 'string', length: 13, nullable: true)]
    private ?string $pagerange;

    /**
     * Legal Deposit Number. FD 10608
     */
    #[Assert\Length(max: 100, maxMessage: 'cave.validator.max.length')]
    #[ORM\Column(name: 'legal_deposit_number', type: 'string', length: 100, nullable: true)]
    private ?string $legaldepositnumber;

    /**
     * @var ?bool Bibliography present? FD 316
     */
    #[ORM\Column(name: 'bibliography_present', type: 'boolean', nullable: true, options: ['fixed' => true])]
    private ?bool $bibliographypresent;

    /**
     * Quantity of maps. FD 317
     */
    #[Assert\Length(max: 3, maxMessage: 'cave.validator.max.length')]
    #[ORM\Column(name: 'quantity_of_maps', type: 'string', length: 3, nullable: true, options: ['fixed' => true])]
    private ?string $quantityofmaps;

    /**
     * Quantity of plates . FD 318 ???
     */
    #[Assert\Length(max: 3, maxMessage: 'cave.validator.max.length')]
    #[ORM\Column(name: 'quantity_of_plates', type: 'string', length: 3, nullable: true, options: ['fixed' => true])]
    private ?string $quantityofplates;

    public function getVolumenumber(): ?string
    {
        return $this->volumenumber;
    }

    public function setVolumenumber(?string $volumenumber): self
    {
        $this->volumenumber = $volumenumber;
        return $this;
    }

    public function getPagerange(): ?string
    {
        return $this->pagerange;
    }

    public function setPagerange(?string $pagerange): self
    {
        $this->pagerange = $pagerange;
        return $this;
    }

    public function getLegaldepositnumber(): ?string
    {
        return $this->legaldepositnumber;
    }

    public function setLegaldepositnumber(?string $legaldepositnumber): self
    {
        $this->legaldepositnumber = $legaldepositnumber;
        return $this;

    }

    public function getBibliographypresent(): ?bool
    {
        return $this->bibliographypresent;
    }

    public function setBibliographypresent(?bool $bibliographypresent): self
    {
        $this->bibliographypresent = $bibliographypresent;
        return $this;
    }


    public function getQuantityofmaps(): ?string
    {
        return $this->quantityofmaps;
    }

    public function setQuantityofmaps(?string $quantityofmaps): self
    {
        $this->quantityofmaps = $quantityofmaps;
        return $this;
    }

    public function getQuantityofplates(): ?string
    {
        return $this->quantityofplates;
    }

    public function setQuantityofplates(?string $quantityofplates): self
    {
        $this->quantityofplates = $quantityofplates;
        return $this;
    }

}

