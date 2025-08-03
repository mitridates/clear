<?php
namespace App\Geonames\Domain\Entity;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'geonames_admin2')]
#[ORM\Entity]
#[ORM\Index(columns: ['country'], name: 'country_idx')]
#[ORM\Index(columns: ['admin1'], name: 'admin1_idx')]
class Admin2
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    #[ORM\Column(name: 'id', type: 'string', length: 50,options: [
        "comment" => "Admin2 code Id = country iso alpha2 + [dot] + admin1 [dot] + admin2"
    ])]
    private string $id;

    #[ORM\Column(name: 'geoname_id', type: 'integer', nullable: false)]
    private ?int $geonameid = null;

    #[ORM\Column(name: 'name', type: 'string', length: 200, nullable: false)]
    private ?string $name = null;

    #[ORM\Column(name: 'nameascii', type: 'string', length: 200, nullable: true)]
    private ?string $nameascii = null;

    #[ORM\ManyToOne(targetEntity: Country::class)]
    #[ORM\JoinColumn(name: 'country', referencedColumnName: 'id', nullable: false)]
    private Country $country;

    #[ORM\ManyToOne(targetEntity: Admin1::class)]
    #[ORM\JoinColumn(name: 'admin1', referencedColumnName: 'id', nullable: false)]
    private ?Admin1 $admin1 = null;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getCode(): string
    {
        return explode('.', $this->getId())[2];
    }

    public function setCountry(Country $country): Admin2
    {
        $this->country = $country;
        return $this;
    }

    public function getCountry(): ?Country
    {
        return $this->country;
    }

    public function setAdmin1(Admin1 $admin1): Admin2
    {
        $this->admin1 = $admin1;
        return $this;
    }

    public function getAdmin1(): ?Admin1
    {
        return $this->admin1;
    }

    public function setName(string $name): Admin2
    {
        $this->name = $name;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setNameascii(?string $nameascii): Admin2
    {
        $this->nameascii = $nameascii;
        return $this;
    }

    public function getNameascii(): ?string
    {
        return $this->nameascii;
    }

    public function setGeonameid(?int $geonameid): Admin2
    {
        $this->geonameid = $geonameid;
        return $this;
    }

    public function getGeonameid(): ?int
    {
        return $this->geonameid;
    }
}