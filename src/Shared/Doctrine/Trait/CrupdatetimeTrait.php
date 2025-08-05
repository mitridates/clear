<?php
namespace  App\Shared\Doctrine\Trait;

use Doctrine\ORM\Mapping as ORM;

/**
 * Fields create, update
 * Require HasLifecycleCallbacks annotation in entity
 */
trait CrupdatetimeTrait
{

    #[ORM\Column(name: 'created', type: 'datetime', nullable: false, options: ['default' => 'CURRENT_TIMESTAMP'])]
    protected ?\DateTime $created= null;

    #[ORM\Column(name: 'updated', type: 'datetime', nullable: false, options: ['default' => 'CURRENT_TIMESTAMP'])]
    protected ?\DateTime $updated= null;

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function updatedTimestamp(): void
    {
        $dateTimeNow = new \DateTime('now');
        $this->setUpdated($dateTimeNow);
        if ($this->created===null){
            $this->setCreated($dateTimeNow);
        }
    }

    public function getCreated(): \DateTime
    {
        if ($this->created===null){
            $this->setCreated(new \DateTime('now'));
        }
        return $this->created;
    }

    public function setCreated(\DateTime $created): self
    {
        $this->created = $created;
        return $this;
    }

    public function getUpdated(): \DateTime
    {
        return $this->updated;
    }

    public function setUpdated(\DateTime $updated): self
    {
        $this->updated = $updated;
        return $this;
    }
}

