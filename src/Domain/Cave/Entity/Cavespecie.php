<?php
namespace  App\Domain\Cave\Entity;
use App\Domain\Article\Entity\Article;
use App\Domain\Cave\Entity\Trait\CaveManyToOneTrait;
use App\Domain\Fielddefinition\Entity\Fieldvaluecode;
use App\Domain\Specie\Entity\Specie;
use  App\Infrastructure\Doctrine\Trait\CrupdatetimeTrait;
use Doctrine\ORM\Mapping as ORM;
use  App\Infrastructure\Doctrine\Trait\SequenceTrait;

/**
 * CA0037 Species found 0:n
 */
#[ORM\Table(name: 'cave_specie')]
#[ORM\Index(columns: ['cave'], name: 'cave_idx')]
#[ORM\Index(columns: ['specie_id'], name: 'specie_id_idx')]
#[ORM\Index(columns: ['article_id'], name: 'article_id_idx')]
#[ORM\Index(columns: ['specie_confidence_level'], name: 'specie_confidence_level_idx')]
#[ORM\Index(columns: ['genus_confidence_level'], name: 'genus_confidence_level_idx')]
#[ORM\Index(columns: ['species_significance'], name: 'species_significance_idx')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class Cavespecie
{
    use SequenceTrait, CrupdatetimeTrait, CaveManyToOneTrait;

    /**
      * FD 227
      */
     #[ORM\ManyToOne(targetEntity: Cave::class, inversedBy: 'cavespecie')]
     #[ORM\JoinColumn(name: 'cave', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
     private Cave $cave;

    /**
     * FD 364
     */
    #[ORM\ManyToOne(targetEntity:  Specie::class)]
    #[ORM\JoinColumn(name: 'specie_id', referencedColumnName: 'id', nullable: true)]
    private ?Specie $specie = null;

    /**
     * ref - article ID. FD 263
     */
    #[ORM\ManyToOne(targetEntity:  Article::class)]
    #[ORM\JoinColumn(name: 'article_id', referencedColumnName: 'id', nullable: true)]
    private ?Article $article = null;

    /**
     * Species confidence level. FD 510
     */
    #[ORM\ManyToOne(targetEntity: Fieldvaluecode::class)]
    #[ORM\JoinColumn(name: 'specie_confidence_level', referencedColumnName: 'id')]
    private ?Fieldvaluecode $specieconfidence = null;    

    /**
     *Genus confidence level. FD 509
     */
    #[ORM\ManyToOne(targetEntity: Fieldvaluecode::class)]
    #[ORM\JoinColumn(name: 'genus_confidence_level', referencedColumnName: 'id')]
    private ?Fieldvaluecode $genusconfidence = null;

    /**
     * Species significance. FD 40
     */
    #[ORM\ManyToOne(targetEntity: Fieldvaluecode::class)]
    #[ORM\JoinColumn(name: 'species_significance', referencedColumnName: 'id')]
    private ?Fieldvaluecode $speciesignificance = null;

    /**
     * Species name . FD 38
     */
    #[ORM\Column(name: 'species_name', type: 'string', length: 30, nullable: true)]
    private ?string $name = null;    

    /**
     * Genus. Code 37
     */
    #[ORM\Column(name: 'genus', type: 'string', length: 30, nullable: true)]
    private ?string $genus = null;    

    /**
     * Species year. Code 260
     */
    #[ORM\Column(name: 'ref_year', type: 'string', length: 4, nullable: true)]
    private ?string $refyear = null;    

    /**
     * Species ref - surname. Code 39
     */
    #[ORM\Column(name: 'ref_surname', type: 'string', length: 45, nullable: true)]
    private ?string $refsurname = null;

    /**
     * Species ref - year suffix. FD 261
     */
    #[ORM\Column(name: 'ref_suffix_year', type: 'string', length: 1, nullable: true, options: ['fixed' => true])]
    private ?string $refyearsuffix = null;

    /**
     * Species ref - comment. Code 262
     */
    #[ORM\Column(name: 'ref_comment', type: 'string', length: 25, nullable: true)]
    private ?string $refcomment = null;

    public function getSpecie(): ?Specie
    {
        return $this->specie;
    }

    public function setSpecie(?Specie $specie): Cavespecie
    {
        $this->specie = $specie;
        return $this;
    }

    public function getArticle(): ?Article
    {
        return $this->article;
    }

    public function setArticle(?Article $article): Cavespecie
    {
        $this->article = $article;
        return $this;
    }

    public function getSpecieconfidence(): ?Fieldvaluecode
    {
        return $this->specieconfidence;
    }

    public function setSpecieconfidence(?Fieldvaluecode $specieconfidence): Cavespecie
    {
        $this->specieconfidence = $specieconfidence;
        return $this;
    }

    public function getGenusconfidence(): ?Fieldvaluecode
    {
        return $this->genusconfidence;
    }

    public function setGenusconfidence(?Fieldvaluecode $genusconfidence): Cavespecie
    {
        $this->genusconfidence = $genusconfidence;
        return $this;
    }

    public function getSpeciesignificance(): ?Fieldvaluecode
    {
        return $this->speciesignificance;
    }

    public function setSpeciesignificance(?Fieldvaluecode $speciesignificance): Cavespecie
    {
        $this->speciesignificance = $speciesignificance;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }
    public function setName(?string $name): Cavespecie
    {
        $this->name = $name;
        return $this;
    }

    public function getGenus(): ?string
    {
        return $this->genus;
    }

    public function setGenus(?string $genus): Cavespecie
    {
        $this->genus = $genus;
        return $this;
    }

    public function getRefyear(): ?string
    {
        return $this->refyear;
    }

    public function setRefyear(?string $refyear): Cavespecie
    {
        $this->refyear = $refyear;
        return $this;
    }

    public function getRefsurname(): ?string
    {
        return $this->refsurname;
    }

    public function setRefsurname(?string $refsurname): Cavespecie
    {
        $this->refsurname = $refsurname;
        return $this;
    }

    public function getRefyearsuffix(): ?string
    {
        return $this->refyearsuffix;
    }

    public function setRefyearsuffix(?string $refyearsuffix): Cavespecie
    {
        $this->refyearsuffix = $refyearsuffix;
        return $this;
    }

    public function getRefcomment(): ?string
    {
        return $this->refcomment;
    }

    public function setRefcomment(?string $refcomment): Cavespecie
    {
        $this->refcomment = $refcomment;
        return $this;
    }
}
