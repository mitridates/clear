<?php
namespace  App\Domain\Article\Entity;
use App\Infrastructure\Doctrine\Trait\CrupdatetimeTrait;
use App\Infrastructure\Doctrine\Trait\HiddenTrait;
use App\Shared\Doctrine\Orm\Id\CavernIdGenerator;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

;

/**
 * Article AR0000.
 */
#[ORM\Table(name: 'article')]
#[ORM\Index(columns: ['country'], name: 'country_idx')]
#[ORM\Index(columns: ['admin1'], name: 'admin1_idx')]
#[ORM\Index(columns: ['link'], name: 'link_idx')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class Article
{
    use HiddenTrait, CrupdatetimeTrait, PublicationTrait;

    /**
     * Article ID FD 307
     */
    #[ORM\Column(name: 'id', type: 'string', length: 17, nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: CavernIdGenerator::class)]
    private string $id;


    /**
     * FD 335
     */
    #[Assert\Length(max: 132, maxMessage: 'cave.validator.max.length')]
    #[ORM\Column(name: 'ref_name_of_article', type: 'string', length: 132, nullable: true)]
    private ?string $articlename = null;

    /**
     * FD 316
     */
    #[ORM\Column(name: 'bibliography_present', type: 'boolean', nullable: true, options: ['fixed' => true])]
    private ?bool $bibliographypresent = null;

    /**
     * FD 317
     */
    #[Assert\Length(max: 3, maxMessage: 'cave.validator.max.length')]
    #[ORM\Column(name: 'quantity_of_maps', type: 'integer', length: 3, nullable: true, options: ['fixed' => true])]
    private ?string $quantityofmaps = null;

    /**
     * Quantity of plates . FD 318 ???
     */
    #[Assert\Length(max: 3, maxMessage: 'cave.validator.max.length')]
    #[ORM\Column(name: 'quantity_of_plates', type: 'string', length: 3, nullable: true, options: ['fixed' => true])]
    private ?string $quantityofplates = null;



    public function getArticlename(): ?string
    {
        return $this->articlename;
    }

    public function setArticlename(?string $articlename): Article
    {
        $this->articlename = $articlename;
        return $this;
    }

    public function getBibliographypresent(): ?bool
    {
        return $this->bibliographypresent;
    }

    public function setBibliographypresent(?bool $bibliographypresent): Article
    {
        $this->bibliographypresent = $bibliographypresent;
        return $this;
    }
 
    public function getQuantityofmaps(): ?string
    {
        return $this->quantityofmaps;
    }
 
    public function setQuantityofmaps(?string $quantityofmaps): Article
    {
        $this->quantityofmaps = $quantityofmaps;
        return $this;
    }
 
    public function getQuantityofplates(): ?string
    {
        return $this->quantityofplates;
    }
 
    public function setQuantityofplates(?string $quantityofplates): Article
    {
        $this->quantityofplates = $quantityofplates;
        return $this;
    }

}