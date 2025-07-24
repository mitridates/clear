<?php
namespace  App\Entity;
use App\Entity\CommonTrait\{CrupdatetimeTrait};
use App\Entity\CommonTrait\HiddenTrait;
use App\Shared\Doctrine\Orm\Id\CavernIdGenerator;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * No PL. Inspired: https://www.student.unsw.edu.au/how-do-i-cite-electronic-sources
 */
#[ORM\Table(name: 'link')]
#[ORM\Index(columns: ['author'], name: 'author_idx')]
#[ORM\Index(columns: ['site_owner'], name: 'organisation_idx')]
#[UniqueEntity(fields: ['urlmd5'], message: 'This url is already in use.', errorPath: 'url',)]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class Link
{
    use HiddenTrait, CrupdatetimeTrait;

    #[ORM\Column(name: 'id', type: 'string', length: 17, nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: CavernIdGenerator::class)]
    private ?string $id=null;

    /**
     * concrete page/article/section
     */
    #[Assert\Length(max: 120)]
    #[ORM\Column(name: 'title', type: 'string', length: 120, nullable: true)]
    private ?string $title = null;

    /**
     * Site owner no ID. FD. 1001 internal
     */
    #[ORM\ManyToOne(targetEntity: Organisation::class)]
    #[ORM\JoinColumn(name: 'site_owner', referencedColumnName: 'id', nullable: true)]
    private ?Organisation $organisation = null;


    /**
     * Site owner. FD. 1002 internal
     */
    #[Assert\Length(max: 120)]
    #[ORM\Column(name: 'site_owner_noid', type: 'string', length: 100, nullable: true)]
    private ?string $organisationname = null;


    /**
     * Author no ID. FD. 1003
     */
    #[ORM\ManyToOne(targetEntity: Person::class)]
    #[ORM\JoinColumn(name: 'author', referencedColumnName: 'id')]
    private ?Person $author = null;

    /**
     * FD. 1004. Author no ID
     */
    #[Assert\Length(max: 120)]
    #[ORM\Column(name: 'author_noid', type: 'string', length: 120, nullable: true)]
    private ?string $authorname = null;

    /**
     * last accessed date. FD. 1005
     */
    #[ORM\Column(name: 'accessed', type: 'date', nullable: true)]
    private ?DateTime $accessed = null;

    /** URL. FD. 1006 */
    #[Assert\Length(max: 512)]
    #[ORM\Column(name: 'url', type: 'text', length: 512, nullable: false)]
    private ?string $url = null;

    /** URL. FD. 1006 */
    #[ORM\Column(name: 'url_md5', type: 'string', length: 32, nullable: false)]
    private ?string $urlmd5 = null;

    /**
     * URL. FD. 1007
     */
    #[Assert\Length(max: 127)]
    #[ORM\Column(name: 'mime', type: 'string', length: 127, nullable: true)]
    private ?string $mime = null;


    public function getId(): ?string
    {
        return $this->id;
    }

    public function getOrganisationname(): ?string
    {
        return $this->organisationname;
    }

    public function setOrganisationname(?string $organisationname): Link
    {
        $this->organisationname = $organisationname;
        return $this;
    }

    public function getOrganisation():?Organisation
    {
        return $this->organisation;
    }

    public function setOrganisation(?Organisation $organisation): Link
    {
        $this->organisation = $organisation;
        return $this;

    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): Link
    {
        $this->title = $title;
        return $this;
    }

    public function getAuthorname(): ?string
    {
        return $this->authorname;
    }

    public function setAuthorname(?string $authorname): Link
    {
        $this->authorname = $authorname;
        return $this;
    }

    public function getAuthor(): ?Person
    {
        return $this->author;
    }

    public function setAuthor(?Person $author): Link
    {
        $this->author = $author;
        return $this;
    }


    public function getAccessed(): ?DateTime
    {
        return $this->accessed;
    }

    public function setAccessed(?DateTime $accessed): Link
    {
        $this->accessed = $accessed;
        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): Link
    {
        $this->url = $url;
        $this->urlmd5= md5($url);
        return $this;
    }


    public function getUrlmd5(): ?string
    {
        return $this->urlmd5;
    }

    public function getMime(): ?string
    {
        return $this->mime;
    }

    public function setMime(?string $mime): Link
    {
        $this->mime = $mime;
        return $this;
    }

}