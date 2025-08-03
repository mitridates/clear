<?php
namespace  App\Cave\Domain\Entity;
use App\Cave\Domain\Entity\Trait\CaveManyToOneTrait;
use App\Infrastructure\Doctrine\Trait\CrupdatetimeTrait;
use App\Infrastructure\Doctrine\Trait\SequenceTrait;
use App\Organisation\Domain\Entity\Organisation;
use Doctrine\ORM\Mapping as ORM;

/**
 * CA0259 Other db site Cave IDs for this cave 0:n
 */
#[ORM\Table(name: 'cave_otherdbid')]
#[ORM\Index(columns: ['cave'], name: 'cave_idx')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class Caveotherdbid
{
    use SequenceTrait, CrupdatetimeTrait, CaveManyToOneTrait;

    /**
      * FD 227
      */
     #[ORM\ManyToOne(targetEntity: Cave::class, inversedBy: 'caveotherdbid')]
     #[ORM\JoinColumn(name: 'cave', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
     private Cave $cave;

    /**
     * Other DB cave IDs. FD 259
     */
    #[ORM\Column(name: 'otherdbid', type: 'string', length: 10, nullable: false)]
    private string $otherdbid;

    /**
     * Discoverer organisation. No FD
     */
    #[ORM\ManyToOne(targetEntity: Organisation::class)]
    #[ORM\JoinColumn(name: 'org_owner_id', referencedColumnName: 'id', nullable: true)]
    private ?Organisation $organisation = null;

    /**
     * Discoverer organisation initials. No FD
     */
    #[ORM\Column(name: 'org_owner_text', type: 'string', length: 30, nullable: true)]
    private ?string $owner = null;

    public function getOtherdbid(): string
    {
        return $this->otherdbid;
    }

    public function setOtherdbid(string $otherdbid): Caveotherdbid
    {
        $this->otherdbid = $otherdbid;
        return $this;
    }

    public function getOrganisation(): ?Organisation
    {
        return $this->organisation;
    }

    public function setOrganisation(?Organisation $organisation): Caveotherdbid
    {
        $this->organisation = $organisation;
        return $this;
    }

    public function getOwner(): ?string
    {
        return $this->owner;
    }

    public function setOwner(?string $owner): Caveotherdbid
    {
        $this->owner = $owner;
        return $this;
    }
}

