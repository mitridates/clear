<?php
namespace  App\Geonames\Domain\Entity\Trait;
use App\Geonames\Domain\Entity\Admin2;
use Doctrine\ORM\Mapping as ORM;

trait Admin2ChildTrait
{
Use Admin1ChildTrait;

    #[ORM\ManyToOne(targetEntity: Admin2::class)]
    #[ORM\JoinColumn(name: 'admin2', referencedColumnName: 'id', nullable: true)]
    private ?Admin2 $admin2;

    public function setAdmin2(?Admin2 $admin2): self
    {
        $this->admin2 = $admin2;
        return $this;
    }

    public function getAdmin2(): ?Admin2
    {
        return $this->admin2;
    }
}

