<?php
namespace  App\Domain\Geonames\Entity\Trait;
use App\Domain\Geonames\Entity\Admin3;
use Doctrine\ORM\Mapping as ORM;

trait Admin3ChildTrait
{
Use Admin2ChildTrait;

    #[ORM\ManyToOne(targetEntity: ' App\Entity\Geonames\Admin3')]
    #[ORM\JoinColumn(name: 'admin3', referencedColumnName: 'id', nullable: true)]
    private ?Admin3 $admin3;

    public function setAdmin3(?Admin3 $admin3): self
    {
        $this->admin3 = $admin3;
        return $this;
    }

    public function getAdmin3(): ?Admin3
    {
        return $this->admin3;
    }
}

