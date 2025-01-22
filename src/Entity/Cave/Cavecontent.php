<?php
namespace  App\Entity\Cave;
use App\Entity\Cave\Trait\CaveManyToOneTrait;
use App\Entity\CommonTrait\{CrupdatetimeTrait};
use App\Entity\CommonTrait\SequenceTrait;
use App\Entity\FieldDefinition\Fieldvaluecode;
use Doctrine\ORM\Mapping as ORM;

/**
 * CA0072 Cave content  0:n
 */
#[ORM\Table(name: 'cave_content')]
#[ORM\Index(columns: ['cave'], name: 'cave_idx')]
#[ORM\Index(columns: ['cave_content'], name: 'fieldvaluecode_cave_content_idx')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class Cavecontent
{
    use SequenceTrait, CrupdatetimeTrait, CaveManyToOneTrait;

    /**
     * FD 227
     */
    #[ORM\ManyToOne(targetEntity: Cave::class, inversedBy: 'cavecontent')]
    #[ORM\JoinColumn(name: 'cave', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private Cave $cave;

    /**
     * FD 72
     */
    #[ORM\ManyToOne(targetEntity: Fieldvaluecode::class)]
    #[ORM\JoinColumn(name: 'cave_content', referencedColumnName: 'id')]
    private ?Fieldvaluecode $content = null;

    public function getContent(): ?Fieldvaluecode
    {
        return $this->content;
    }

    public function setContent(Fieldvaluecode $content): Cavecontent
    {
        $this->content = $content;
        return $this;
    }
}

