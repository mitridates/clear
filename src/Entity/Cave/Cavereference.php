<?php
namespace  App\Entity\Cave;
use App\Domain\Article\Entity\Article;
use App\Entity\Cave\Trait\CaveManyToOneTrait;
use App\Entity\CommonTrait\{CrupdatetimeTrait};
use App\Entity\CommonTrait\SequenceTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * CA0071 Cave reference  0:n
 */
#[ORM\Table(name: 'cave_reference')]
#[ORM\Index(columns: ['cave'], name: 'cave_idx')]
#[ORM\Index(columns: ['article_id'], name: 'article_idx')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class Cavereference
{
    use SequenceTrait, CrupdatetimeTrait, CaveManyToOneTrait;

    /**
      * FD 227
      */
     #[ORM\ManyToOne(targetEntity: Cave::class, inversedBy: 'cavereference')]
     #[ORM\JoinColumn(name: 'cave', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
     private Cave $cave;

    /**
     * @FD 270
     */
    #[ORM\ManyToOne(targetEntity:  Article::class)]
    #[ORM\JoinColumn(name: 'article_id', referencedColumnName: 'id', nullable: true)]
    private ?Article $article = null;

    /**
     * Ref - subjects. FD 268
     */
    #[ORM\Column(name: 'ref_subjects', type: 'string', length: 25, nullable: true)]
    private ?string $subjects = null;

    /**
     * Relevant page range. FD 269
     */
    #[ORM\Column(name: 'ref_relevant_page_range', type: 'string', length: 20, nullable: true)]
    private ?string $range = null;

    /**
     * Ref - surnames. FD 71. If not avail. by 270 direct link.
     */
    #[Assert\Length(max: 45, maxMessage: 'cave.validator.max.length')]
    #[ORM\Column(name: 'ref_surnames', type: 'string', length: 45, nullable: true)]
    private ?string $surnames = null;

    /**
     * Ref - year of publication. FD 266. If not avail. by 270 direct link.
     */
    #[Assert\Length(min: 4, max: 4, exactMessage: 'cave.validator.exact.length')]
    #[ORM\Column(name: 'ref_year_of_publication', type: 'smallint', length: 4, nullable: true, options: ['unsigned' => true, 'fixed' => true])]
    private ?int $year = null;    

    /**
     * Ref - suffix to year. FD 267. If not avail. by 270 direct link.
     */
    #[Assert\Range(min: 1, max: 9)]
    #[ORM\Column(name: 'ref_suffix_to_year', type: 'string', length: 1, nullable: true)]
    private ?string $yearsuffix = null;        

    /**
     * Ref - name of article. FD 355. If not avail. by 270 direct link.
     */
    #[Assert\Length(max: 132, maxMessage: 'cave.validator.max.length')]
    #[ORM\Column(name: 'ref_name_of_article', type: 'string', length: 132, nullable: true)]
    private ?string $articlename = null;

    /**
     * Ref - name of article. FD 356. If not avail. by 270 direct link.
     */
    #[Assert\Length(max: 132, maxMessage: 'cave.validator.max.length')]
    #[ORM\Column(name: 'ref_name_of_publication', type: 'string', length: 132, nullable: true)]
    private ?string $publication = null;    

    /**
     * Ref - volume number. FD 357. If not avail. by 270 direct link.
     */
    #[Assert\Length(max: 7, maxMessage: 'cave.validator.max.length')]
    #[ORM\Column(name: 'ref_volume_number', type: 'string', length: 7, nullable: true)]
    private ?string $volume = null;  

    /**
     * Ref - issue number. FD 358. If not avail. by 270 direct link.
     */
    #[Assert\Length(max: 4, maxMessage: 'cave.validator.max.length')]
    #[ORM\Column(name: 'ref_issue_number', type: 'string', length: 4, nullable: true)]
    private ?string $issue = null;

    public function getArticle(): ?Article
    {
        return $this->article;
    }

    public function setArticle(?Article $article): Cavereference
    {
        $this->article = $article;
        return $this;
    }

    public function getSubjects(): ?string
    {
        return $this->subjects;
    }

    public function setSubjects(?string $subjects): Cavereference
    {
        $this->subjects = $subjects;
        return $this;
    }

    public function getRange(): ?string
    {
        return $this->range;
    }

    public function setRange(?string $range): Cavereference
    {
        $this->range = $range;
        return $this;
    }

    public function getSurnames(): ?string
    {
        return $this->surnames;
    }

    public function setSurnames(?string $surnames): Cavereference
    {
        $this->surnames = $surnames;
        return $this;
    }

    public function getYear(): ?int
    {
        return $this->year;
    }

    public function setYear(?int $year): Cavereference
    {
        $this->year = $year;
        return $this;
    }

    public function getYearsuffix(): ?string
    {
        return $this->yearsuffix;
    }

    public function setYearsuffix(?string $yearsuffix): Cavereference
    {
        $this->yearsuffix = $yearsuffix;
        return $this;
    }

    public function getArticlename(): ?string
    {
        return $this->articlename;
    }

    public function setArticlename(?string $articlename): Cavereference
    {
        $this->articlename = $articlename;
        return $this;
    }

    public function getPublication(): ?string
    {
        return $this->publication;
    }

    public function setPublication(?string $publication): Cavereference
    {
        $this->publication = $publication;
        return $this;
    }

    public function getVolume(): ?string
    {
        return $this->volume;
    }

    public function setVolume(?string $volume): Cavereference
    {
        $this->volume = $volume;
        return $this;
    }

    public function getIssue(): ?string
    {
        return $this->issue;
    }

    public function setIssue(?string $issue): Cavereference
    {
        $this->issue = $issue;
        return $this;
    }
}
