<?php
namespace  App\Entity\Article;
use App\Entity\CommonTrait\{CrupdatetimeTrait};
use App\Entity\CommonTrait\SequenceTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * AR0331 0:n
 * Persona/s  o  entidad  corporativa responsable de contribuir en la creación o realización del contenido intelectual o artístico. Para una relación individualizada utilizar 331[Author surname] y 332[Author initials]
 *
 * Apellidos de los autores o responsables separados por punto y coma ';'.
 * El nombre puede ir detrás del apellido separado por coma ','.
 * Para abreviar, las omisiones se indican " ... [et al.]" (... y otros) después del último autor.
 *
 * (1) Un autor: Fernández Manzano (2) Más de uno: Doe; Roe; González Pérez (3) Apellidos, Nombre:
 * Doe, John; Roe, Richard; González Pérez, Juan (4) Omisiones : Doe, John; Roe, Richard; González Pérez, Juan ... [et al.]
 */
#[ORM\Table(name: 'article_author')]
#[ORM\Index(columns: ['article'], name: 'article_idx')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class Articleauthor
{
    use SequenceTrait, CrupdatetimeTrait;

    /**
      * FD 227
      */
     #[ORM\ManyToOne(targetEntity:  Article::class, inversedBy: 'articleauthor')]
     #[ORM\JoinColumn(name: 'article', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
     protected Article $article;

    /**
     * No FD
     */
    #[ORM\Column(name: 'author_name', type: 'string', length: 20, nullable: true)]
    private ?string $name = null;

    /**
     * FD. 331
     */
    #[ORM\Column(name: 'author_surname', type: 'string', length: 20, nullable: true)]
    private ?string $surname = null;

    /**
     * FD. 332
     */
    #[ORM\Column(name: 'author_initials', type: 'string', length: 15, nullable: true)]
    private ?string $initials = null;

    public function getArticle(): Article
    {
        return $this->article;
    }

    public function setArticle(Article $article): Articleauthor
    {
        $this->article = $article;
        return $this;
    }

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(?string $surname): Articleauthor
    {
        $this->surname = $surname;
        return $this;
    }
 
    public function getName(): ?string
    {
        return $this->name;
    }
 
    public function setName(?string $name): Articleauthor
    {
        $this->name = $name;
        return $this;
    }
 
    public function getInitials(): ?string
    {
        return $this->initials;
    }
 
    public function setInitials(?string $initials): Articleauthor
    {
        $this->initials = $initials;
        return $this;
    }
}