<?php
namespace  App\Entity\Article;
use App\Entity\Geonames\{Admin1, Country};
use App\Entity\Link;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

trait PublicationTrait
{
    /**
     *  FD 310
     */
    #[ORM\Column(name: 'author_editor', type: 'string', length: 45, nullable: true)]
    #[Assert\Length(max: 45, maxMessage: 'cave.validator.max.length')]
    private ?string $authororeditor=null;

    /**
     * FD 333
     */
    #[ORM\Column(name: 'comments', type: 'json', nullable: true)]
    private ?array $comments=null;

    /**
     * No FD
     */
    #[ORM\Column(name: 'contributors', type: 'json', nullable: true)]
    private ?array $contributors=null;

    /**
     * FD 311
     */
    #[ORM\Column(name: 'ref_name_of_publication', type: 'string', length: 132, nullable: true)]
    #[Assert\Length(max: 132, maxMessage: 'cave.validator.max.length')]
    private ?string $publicationname=null;

    /**
     * FD 308
     */
    #[Assert\Length(min: 4, max: 4, exactMessage: 'cave.validator.exact.length')]
    #[ORM\Column(name: 'ref_year_of_publication', type: 'smallint', length: 4, nullable: true, options: ['unsigned' => true])]
    private ?int $publicationyear=null;

    /**
     * FD 309
     */
    #[Assert\Length(min: 1, max: 1, exactMessage: 'cave.validator.exact.length')]
    #[ORM\Column(name: 'ref_suffix_to_year', type: 'string', length: 1, nullable: true, options: ['fixed' => true])]
    private ?string $publicationyearsuffix=null;

    /**
     * FD 312
     */
    #[Assert\Length(max: 7, maxMessage: 'cave.validator.max.length')]
    #[ORM\Column(name: 'volume_number', type: 'string', length: 7, nullable: true)]
    private ?string $volumenumber=null;

    /**
     *  FD 313
     */
    #[Assert\Length(max: 4, maxMessage: 'cave.validator.max.length')]
    #[ORM\Column(name: 'issue_number', type: 'string', length: 4, nullable: true)]
    private ?string $issuenumber=null;


    /**
     * FD 314
     */
    #[Assert\Length(max: 45, maxMessage: 'cave.validator.max.length')]
    #[ORM\Column(name: 'book_publisher_and_city', type: 'string', length: 45, nullable: true)]
    private ?string $bookpublisherandcity=null;


    /**
     * Page range or quantity. FD 315
     */
    #[Assert\Length(max: 13, maxMessage: 'cave.validator.max.length')]
    #[ORM\Column(name: 'page_range_or_quantity', type: 'string', length: 13, nullable: true)]
    private ?string $pagerange=null;


    /**
     * FD 608
     */
    #[Assert\Length(min: 10, max: 10, exactMessage: 'cave.validator.exact.length')]
    #[ORM\Column(name: 'isbn', type: 'string', length: 10, nullable: true)]
    private ?string $isbn=null;


    /**
     * No FD
     */
    #[Assert\Length(max: 200)]
    #[ORM\Column(name: 'copyright', type: 'string', length: 200, nullable: true)]
    private ?string $copyright=null;

    /**
     * FD 320
     */
    #[Assert\Length(min: 9, max: 9, exactMessage: 'cave.validator.exact.length')]
    #[ORM\Column(name: 'issn', type: 'string', length: 10, nullable: true)]
    private ?string $issn=null;


    /**
     * FD 10608
     */
    #[Assert\Length(max: 100, maxMessage: 'cave.validator.max.length')]
    #[ORM\Column(name: 'legal_deposit_number', type: 'string', length: 100, nullable: true)]
    private ?string $legaldepositnumber=null;


    /**
     * FK. FD 319
     */
    #[ORM\ManyToOne(targetEntity: Country::class)]
    #[ORM\JoinColumn(name: 'country', referencedColumnName: 'id', nullable: true)]
    private ?Country $country=null;

    /**
     * FK. State supplying this ref. FD 321
     */
    #[ORM\ManyToOne(targetEntity: Admin1::class)]
    #[ORM\JoinColumn(name: 'admin1', referencedColumnName: 'id', nullable: true)]
    private ?Admin1 $admin1=null;


    /**
     * No FD
     */
    #[ORM\ManyToOne(targetEntity: Link::class)]
    #[ORM\JoinColumn(name: 'link', referencedColumnName: 'id', nullable: true)]
    private ?Link $link=null;

    /**
     *  FD. 1006
     */
    #[Assert\Length(max: 512)]
    #[ORM\Column(name: 'url', type: 'text', length: 512, nullable: true)]
    private ?string $url=null;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getAuthororeditor(): ?string
    {
        return $this->authororeditor;
    }

    public function setAuthororeditor(?string $authororeditor): self
    {
        $this->authororeditor = $authororeditor;
        return $this;
    }

    public function getContributors(): ?array
    {
        return $this->contributors;
    }

    public function setContributors(?array $contributors): self
    {
        $this->contributors = $contributors;
        return $this;
    }

    public function getComments(): ?array
    {
        return $this->comments;
    }

    public function setComments(?array $comments): self
    {
        $this->comments = $comments;
        return $this;
    }

    public function getPublicationname(): ?string
    {
        return $this->publicationname;
    }

    public function setPublicationname(?string $publicationname): self
    {
        $this->publicationname = $publicationname;
        return $this;
    }

    public function getPublicationyear(): ?int
    {
        return $this->publicationyear;
    }

    public function setPublicationyear(?int $publicationyear): self
    {
        $this->publicationyear = $publicationyear;
        return $this;
    }

    public function getPublicationyearsuffix(): ?string
    {
        return $this->publicationyearsuffix;
    }

    public function setPublicationyearsuffix(?string $publicationyearsuffix): self
    {
        $this->publicationyearsuffix = $publicationyearsuffix;
        return $this;
    }

    public function getVolumenumber(): ?string
    {
        return $this->volumenumber;
    }

    public function setVolumenumber(?string $volumenumber): self
    {
        $this->volumenumber = $volumenumber;
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

    public function getBookpublisherandcity(): ?string
    {
        return $this->bookpublisherandcity;
    }

    public function setBookpublisherandcity(?string $bookpublisherandcity): self
    {
        $this->bookpublisherandcity = $bookpublisherandcity;
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

    public function getIsbn(): ?string
    {
        return $this->isbn;
    }

    public function setIsbn(?string $isbn): self
    {
        $this->isbn = $isbn;
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
 
    public function getIssn(): ?string
    {
        return $this->issn;
    }
 
    public function setIssn(?string $issn): self
    {
        $this->issn = $issn;
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

    public function getCountry(): ?Country
    {
        return $this->country;
    }

    public function setCountry(?Country $country): self
    {
        $this->country = $country;
        return $this;
    }

    public function getAdmin1(): ?Admin1
    {
        return $this->admin1;
    }

    public function setAdmin1(?Admin1 $admin1): self
    {
        $this->admin1 = $admin1;
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

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): self
    {
        $this->url = $url;
        return $this;
    }
}

