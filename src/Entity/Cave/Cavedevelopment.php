<?php
namespace  App\Entity\Cave;
use App\Entity\Cave\Trait\CaveManyToOneTrait;
use App\Entity\CommonTrait\{CrupdatetimeTrait};
use App\Entity\CommonTrait\SequenceTrait;
use App\Entity\FieldDefinition\Fieldvaluecode;
use Doctrine\ORM\Mapping as ORM;

/**
 * CA0011 Cave Cavedevelopment 0:n
 */
#[ORM\Table(name: 'cave_development')]
#[ORM\Index(columns: ['cave'], name: 'cave_idx')]
#[ORM\Index(columns: ['development'], name: 'fieldvaluecode_development_idx')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class Cavedevelopment
{
    use SequenceTrait, CrupdatetimeTrait, CaveManyToOneTrait;

    /**
     * FD 227
     */
    #[ORM\ManyToOne(targetEntity: Cave::class, inversedBy: 'cavedevelopment')]
    #[ORM\JoinColumn(name: 'cave', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private Cave $cave;

    /**
     * FD 11
     */
    #[ORM\ManyToOne(targetEntity: Fieldvaluecode::class)]
    #[ORM\JoinColumn(name: 'development', referencedColumnName: 'id', nullable: false)]
    private ?Fieldvaluecode $development = null;

    public function getDevelopment(): ?Fieldvaluecode
    {
        return $this->development;
    }

    public function setDevelopment(Fieldvaluecode $development): Cavedevelopment
    {
        $this->development = $development;
        return $this;
    }
}

