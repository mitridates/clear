<?php
namespace  App\Domain\Citation\Entity;
use App\Shared\Doctrine\Orm\Id\CavernIdGenerator;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Cache\Exception\InvalidArgumentException;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'citation')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class Citation
{
//    Use OnlineResourceTrait;
    const BOOK_TYPE=1;
    const BOOK_CARPET_TYPE=2;
    const JOURNAL_ARTICLE_TYPE=3;
    const WEBPAGE_TYPE=4;
    const WEBSITE_TYPE=5;
    const ONLINE_ARTICLE_TYPE= 6; //===self::WEBPAGE_TYPE;
    const ONLINE_MAGAZINE_ARTICLE_TYPE=7;

    #[ORM\Column(name: 'id', type: 'string', length: 17, nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: CavernIdGenerator::class)]
    private ?string $id=null;
    /**
     * Citation type. FD ?
     */
    #[ORM\Column(name: 'type', type: 'smallint', length: 2, nullable: false)]
    private ?int $type = null;

    #[Assert\Length(max: 512, maxMessage: 'cave.validator.max.length')]
    #[ORM\Column(name: 'title', type: 'string', length: 512, nullable: false)]
    private ?string $title = null;


    #[Assert\Length(max: 512, maxMessage: 'cave.validator.max.length')]
    #[ORM\Column(name: 'subtitle', type: 'string', length: 512, nullable: true)]
    private ?string $subtitle = null;


    #[Assert\Length(max: 512, maxMessage: 'cave.validator.max.length')]
    #[ORM\Column(name: 'containertitle', type: 'string', length: 512, nullable: true)]
    private ?string $containertitle = null;

    #[Assert\Length(max: 2, maxMessage: 'cave.validator.max.length')]
    #[ORM\Column(name: 'country', type: 'string', length: 2, nullable: true)]
    private ?string $country = null;

    /**Publisher region... json en publisher?*/
    #[Assert\Length(max: 45, maxMessage: 'cave.validator.max.length')]
    #[ORM\Column(name: 'region', type: 'string', length: 45, nullable: true)]
    private ?string $region = null;
    /**Publisher city... json en publisher?*/
    #[Assert\Length(max: 45, maxMessage: 'cave.validator.max.length')]
    #[ORM\Column(name: 'city', type: 'string', length: 45, nullable: true)]
    private ?string $city = null;

    #[Assert\Length(max: 512, maxMessage: 'cave.validator.max.length')]
    #[ORM\Column(name: 'jsondata', type: 'text',  nullable: true)]
    private ?string $jsondata=null;
    /**
     * FD. n.d. json contributors
     */
    #[Assert\Length(max: 512, maxMessage: 'cave.validator.max.length')]
    #[ORM\Column(name: 'contributor', type: 'text',  nullable: true)]
    private ?string $contributor=null;
    
    /**
     * FD. 10609 internal
     */
    #[Assert\Length(max: 512)]
    #[ORM\Column(name: 'comment', type: 'text', length: 512, nullable: true)]
    private ?string $comment = null;

    /**
     *  Table of content. FD 10610 internal
     */
    #[Assert\Length(max: 1500)]
    #[ORM\Column(name: 'content', type: 'text', length: 1500, nullable: true)]
    private ?string $content = null;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    /**
     * @param ?int $type
     * @return string
     */
    public function typeToString(?int $type=null): string
    {
        $t= $type??$this->type;
        return match($t) {
            0 => 'book',
            self::BOOK_TYPE => 'book',
            self::BOOK_CARPET_TYPE => 'book-charpet',
            self::JOURNAL_ARTICLE_TYPE => 'journal-article',
            self::WEBPAGE_TYPE => 'webpage',
            self::WEBSITE_TYPE => 'website',
            self::ONLINE_ARTICLE_TYPE => 'online-article',
            self::ONLINE_MAGAZINE_ARTICLE_TYPE => 'online-magazine-article',
            default => throw new InvalidArgumentException(sprintf('Citation Type "%s" not found', $t)),
        };
    }
    public function setType(?int $type): Citation
    {
        $this->type = $type;
        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): Citation
    {
        $this->title = $title;
        return $this;
    }

    public function getSubtitle(): ?string
    {
        return $this->subtitle;
    }

    public function setSubtitle(?string $subtitle): Citation
    {
        $this->subtitle = $subtitle;
        return $this;
    }

    public function getContainertitle(): ?string
    {
        return $this->containertitle;
    }

    public function setContainertitle(?string $containertitle): Citation
    {
        $this->containertitle = $containertitle;
        return $this;
    }


    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): Citation
    {
        $this->country = $country;
        return $this;
    }


    public function getPublisher(): ?string
    {
        return $this->publisher;
    }

    public function setPublisher(?string $publisher): Citation
    {
        $this->publisher = $publisher;
        return $this;
    }

    public function getRegion(): ?string
    {
        return $this->region;
    }

    public function setRegion(?string $region): Citation
    {
        $this->region = $region;
        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): Citation
    {
        $this->city = $city;
        return $this;
    }

    public function getJsondata(): ?string
    {
        return $this->jsondata;
    }

    public function setJsondata(?string $jsondata): Citation
    {
        $this->jsondata = $jsondata;
        return $this;
    }

    public function getContributor(): ?string
    {
        return $this->contributor;
    }

    public function setContributor(?string $contributor): Citation
    {
        $this->contributor = $contributor;
        return $this;
    }

    
    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): Citation
    {
        $this->comment = $comment;
        return $this;
    }
    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): Citation
    {
        $this->content = $content;
        return $this;
    }

}

