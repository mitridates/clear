<?php
namespace  App\Cave\Domain\Entity;
use App\Cave\Domain\Entity\Trait\CaveManyToOneTrait;
use App\Shared\Doctrine\Trait\CrupdatetimeTrait;
use App\Shared\Doctrine\Trait\SequenceTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * CA0035 Cave extension discovery 0:n
 */
#[ORM\Table(name: 'cave_extensiondiscovery')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class Caveextensiondiscovery
{
    use SequenceTrait, CrupdatetimeTrait, CaveManyToOneTrait;

    /**
     * FD 227
     */
    #[ORM\ManyToOne(targetEntity: Cave::class, inversedBy: 'caveextensiondiscovery')]
    #[ORM\JoinColumn(name: 'cave', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private Cave $cave;

    /**
     * FD 35
     */
    #[ORM\Column(name: 'extension_discovery', type: 'string', length: 52, nullable: false)]
    private ?string $extensiondiscovery = null;

    public function getExtensiondiscovery(): string
    {
        return $this->extensiondiscovery;
    }

    public function setExtensiondiscovery(string $extensiondiscovery): Caveextensiondiscovery
    {
        $this->extensiondiscovery = $extensiondiscovery;
        return $this;
    }
}

