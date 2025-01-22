<?php
namespace  App\Entity\Geonames\Trait;
use App\Entity\Geonames\Country;
use Doctrine\ORM\Mapping as ORM;

trait CountryTrait
{

    #[ORM\ManyToOne(targetEntity: Country::class)]
    #[ORM\JoinColumn(name: 'country', referencedColumnName: 'id', nullable: true)]
    private ?Country $country=null;

    public function getCountry(): ?Country
    {
        return $this->country;
    }

    public function setCountry(?Country $country): self
    {
        $this->country = $country;
        return $this;
    }
}

