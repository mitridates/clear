<?php
namespace  App\Domain\Geonames\Entity\Trait;
use App\Domain\Geonames\Entity\Admin1;
use Doctrine\ORM\Mapping as ORM;

trait Admin1ChildTrait
{
Use CountryTrait;

    #[ORM\ManyToOne(targetEntity: Admin1::class)]
    #[ORM\JoinColumn(name: 'admin1', referencedColumnName: 'id', nullable: true)]
    private ?Admin1 $admin1;

    public function getAdmin1(): ?Admin1
    {
        return $this->admin1;
    }

    public function setAdmin1(?Admin1 $admin1): self
    {
        $this->admin1 = $admin1;
        return $this;
    }
}

