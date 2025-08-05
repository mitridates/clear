<?php
namespace  App\Shared\Doctrine\Trait;

use App\Link\Domain\Entity\Link;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Link/url
 */
trait   OnlineResourceTrait
{
    /**
     * FD. 1006. URL
     */
    #[Assert\Length(max: 512)]
    #[ORM\Column(name: 'url', type: 'text', length: 512, nullable: true)]
    private ?string $url;

    /**
     * No FD
     */
    #[ORM\ManyToOne(targetEntity: 'App\Entity\Link')]
    #[ORM\JoinColumn(name: 'link', referencedColumnName: 'id', nullable: true)]
    private ?Link $link;

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): self
    {
        $this->url = $url;
        return $this;
    }

    public function getLink(): ?Link
    {
        return $this->link;
    }

    public function setLink(?Link $link): self
    {
        $this->link = $link;
        return $this;
    }
}

