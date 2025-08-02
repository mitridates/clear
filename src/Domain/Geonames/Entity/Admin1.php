<?php
namespace App\Domain\Geonames\Entity;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'geonames_admin1')]
#[ORM\Entity]
#[ORM\Index(columns: ['country'], name: 'country_idx')]

class Admin1
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    #[ORM\Column(name: 'id', type: 'string', length: 20,options: [
        "comment" => "Admin1 Id = country iso 2 + [dot] + admin1 code."
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
    private ?Country $country = null;

    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @return ?string Admin1 code
     */
    public function getCode(): ?string
    {
        return explode('.', $this->id)[1];
    }

    public function setGeonameid(?int $geonameid): Admin1
    {
        $this->geonameid = $geonameid;
        return $this;
    }

    /**
     * @return ?int
     */
    public function getGeonameid(): ?int
    {
        return $this->geonameid;
    }

    public function setName(string $name): Admin1
    {
        $this->name = $name;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setNameascii(?string $nameascii): Admin1
    {
        $this->nameascii = $nameascii;
        return $this;
    }

    public function getNameascii(): ?string
    {
        return $this->nameascii;
    }

    public function getCountry(): ?Country
    {
        return $this->country;
    }

    public function setCountry(?Country $country): Admin1
    {
        $this->country = $country;
        return $this;
    }
}