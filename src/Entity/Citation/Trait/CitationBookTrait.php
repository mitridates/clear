<?php
namespace  App\Entity\Citation\Trait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Citation book trait
 */
trait CitationBookTrait
{
use CitationBookArticleBaseTrait;

    /**
     * Book publisher and city. FD 314
     */
    #[Assert\Length(max: 45, maxMessage: 'cave.validator.max.length')]
    #[ORM\Column(name: 'book_publisher', type: 'string', length: 45, nullable: true)]
    private ?string $publisher;


    /**
     * ISBN . FD 608
     */
    #[Assert\Length(min: 10, max: 10, exactMessage: 'cave.validator.exact.length')]
    #[ORM\Column(name: 'isbn', type: 'string', length: 10, nullable: true)]
    private ?string $isbn;

    public function getPublisher(): ?string
    {
        return $this->publisher;
    }

    public function setPublisher(?string $publisher): self
    {
        $this->publisher = $publisher;
        return $this;
    }

    public function getIsbn(): ?string
    {
        return $this->isbn;
    }

    public function setIsbn(?string $isbn): self
    {
        $this->isbn = $isbn;
        return $this;
    }

}

