<?php
namespace  App\Entity\Cave;
use App\Domain\Fielddefinition\Entity\Fieldvaluecode;
use App\Entity\Cave\Trait\CaveManyToOneTrait;
use App\Entity\CommonTrait\{CrupdatetimeTrait};
use App\Entity\CommonTrait\SequenceTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * CA0258  Cave access 0:n
 */
#[ORM\Table(name: 'cave_access')]
#[ORM\Index(columns: ['cave'], name: 'cave_idx')]
#[ORM\Index(columns: ['access_status'], name: 'fieldvaluecode_access_status_idx')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]

class Caveaccess
{
    use SequenceTrait, CrupdatetimeTrait, CaveManyToOneTrait;

    /**
      * @var Cave ID. FD 227
      */
     #[ORM\ManyToOne(targetEntity: Cave::class, inversedBy: 'caveaccess')]
     #[ORM\JoinColumn(name: 'cave', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
     private Cave $cave;

    /**
     *  FD 258
     */
    #[ORM\ManyToOne(targetEntity: Fieldvaluecode::class)]
    #[ORM\JoinColumn(name: 'access_status', referencedColumnName: 'id', nullable: false)]
    private ?Fieldvaluecode $access = null;

    public function getAccess(): ?Fieldvaluecode
    {
        return $this->access;
    }

    public function setAccess(Fieldvaluecode $access): Caveaccess
    {
        $this->access = $access;
        return $this;
    }
}

