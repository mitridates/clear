<?php

namespace App\Entity\Geonames;
use App\Repository\Geonames\CommonRepository;
use Doctrine\ORM\Mapping as ORM;
/**
 * Admin3 code for third administrative division, a county in the US, see file admin3Codes.txt; varchar(80)
 */
#[ORM\Table(name: 'geonames_admin3')]
#[ORM\Entity(repositoryClass: CommonRepository::class, readOnly: true)]
#[ORM\Index(columns: ['country'], name: 'country_idx')]
#[ORM\Index(columns: ['admin1'], name: 'admin1_idx')]
#[ORM\Index(columns: ['admin2'], name: 'admin2_idx')]
class Admin3
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    #[ORM\Column(name: 'id', type: 'string', length: 30,options: [
        "comment" => "country + [dot] + admin1 + [dot] + admin2 + [dot] + admin3"
    ])]
    private string $id;

    #[ORM\Column(name: 'geoname_id', type: 'integer', nullable: false)]
    private ?int $geonameid = null;

    /**
     * Name of geographical point (utf8)
     */
    #[ORM\Column(name: 'name', type: 'string', length: 200, nullable: false)]
    private ?string $name = null;

    /**
     *  Name of geographical point in plain ascii characters
     */
    #[ORM\Column(name: 'nameascii', type: 'string', length: 200, nullable: true)]
    private ?string $nameascii = null;

    #[ORM\ManyToOne(targetEntity: Country::class)]
    #[ORM\JoinColumn(name: 'country', referencedColumnName: 'id', nullable: false)]
    private ?Country $country = null;

    #[ORM\ManyToOne(targetEntity: Admin1::class)]
    #[ORM\JoinColumn(name: 'admin1', referencedColumnName: 'id', nullable: false)]
    private ?Admin1 $admin1 = null;

    #[ORM\ManyToOne(targetEntity: Admin2::class)]
    #[ORM\JoinColumn(name: 'admin2', referencedColumnName: 'id', nullable: true)]
    private ?Admin2 $admin2 = null;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getCode(): string
    {
        return explode('.', $this->getId())[3];
    }

    public function setCountry(Country $country): Admin3
    {
        $this->country = $country;
        return $this;
    }

    public function getCountry(): ?Country
    {
        return $this->country;
    }

    public function getAdmin1(): ?Admin1
    {
        return $this->admin1;
    }

    public function setAdmin1(Admin1 $admin1): Admin3
    {
        $this->admin1 = $admin1;
        return $this;
    }

    public function getAdmin2(): ?Admin2
    {
        return $this->admin2;
    }

    public function setAdmin2(?Admin2 $admin2): Admin3
    {
        $this->admin2 = $admin2;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): Admin3
    {
        $this->name = $name;
        return $this;
    }

    public function getNameascii(): ?string
    {
        return $this->nameascii;
    }

    public function setNameascii(?string $nameascii): Admin3
    {
        $this->nameascii = $nameascii;
        return $this;
    }

    /**
     * @return ?int
     */
    public function getGeonameid(): ?int
    {
        return $this->geonameid;
    }

    public function setGeonameid(?int $geonameid): Admin3
    {
        $this->geonameid = (int)$geonameid;
        return $this;
    }
}
