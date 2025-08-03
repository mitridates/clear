<?php

namespace App\Geonames\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'geonames_country')]
#[ORM\Entity]
class Country
{
    # underscore separator
    public static array $CONTINENTS = ['AF'=>6_255_146, 'AS'=>6_255_147, 'EU'=>6_255_148, 'NA'=>6_255_149, 'OC'=>6_255_151, 'SA'=>6_255_150, 'AN'=>6_255_152];

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    #[ORM\Column(name: 'id', type: 'string', length: 20,options: [
        "comment" => "ISO-3166 2-letter country code, 2 characters"
    ])]
    private string $id;

    #[ORM\Column(name: 'geoname_id', type: 'integer', nullable: false)]
    private int $geonameid;

    #[ORM\Column(name: 'isoalpha3', type: 'string', nullable: true, options: [
        "comment" => "Iso 3-letter country"
    ])]
    private string $isoalpha3;

    #[ORM\Column(name: 'isonumeric', type: 'integer', nullable: true, options: [
        "comment" => " Iso number country"
    ])]
    private int $isonumeric;

    #[ORM\Column(name: 'fipscode', type: 'string', length: 3, nullable: true)]
    private string $fipscode;

    #[ORM\Column(name: 'name', type: 'string', length: 200, nullable: false)]
    private string $name;

    #[ORM\Column(name: 'capital', type: 'string', length: 200, nullable: true)]
    private ?string $capital = null;

    #[ORM\Column(name: 'areainsqkm', type: 'decimal', precision: 10, scale: 0, nullable: true)]
    private ?float $areainsqkm = null;

    #[ORM\Column(name: 'population', type: 'integer', nullable: true)]
    private ?int $population = null;

    /**
     * @var ?string
     */
    #[ORM\Column(name: 'continent_iso', type: 'string', length: 2, nullable: false, options: [
        "comment" => "Continent iso 2"
    ])]
    private ?string $continent = null;

    #[ORM\Column(name: 'continent_id', type: 'integer', length: 7, nullable: false, options: [
        "comment" => "continent geonames id"
    ])]
    private int $continentid;

    #[ORM\Column(name: 'tld', type: 'string', length: 3, nullable: true)]
    private ?string $tld = null;

    #[ORM\Column(name: 'currency', type: 'string', length: 3, nullable: true)]
    private ?string $currency = null;

    #[ORM\Column(name: 'currencyname', type: 'string', length: 20, nullable: true)]
    private ?string $currencyname = null;

    #[ORM\Column(name: 'phone', type: 'string', length: 10, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column(name: 'postalcodeformat', type: 'string', length: 20, nullable: true)]
    private ?string $postalcodeformat = null;

    #[ORM\Column(name: 'postalcoderegex', type: 'string', length: 200, nullable: true)]
    private ?string $postalcoderegex = null;

    #[ORM\Column(name: 'languages', type: 'string', length: 200, nullable: true)]
    private ?string $languages = null;

    #[ORM\Column(name: 'neighbours', type: 'string', length: 60, nullable: true)]
    private ?string $neighbours = null;

    #[ORM\Column(name: 'equivalentfipscode', type: 'string', length: 10, nullable: true)]
    private ?string $equivalentfipscode = null;

    public function getId(): ?string
    {
        return $this->id;
    }
    public function getIsoalpha2(): ?string
    {
        return $this->id;
    }
    public function setIsoalpha3(?string $isoalpha3): Country
    {
        $this->isoalpha3 = $isoalpha3;
        return $this;
    }
    public function getIsoalpha3(): ?string
    {
        return $this->isoalpha3;
    }
    public function setIsonumeric(?int $isonumeric): Country
    {
        $this->isonumeric = (int)$isonumeric;
        return $this;
    }
    public function getIsonumeric(): ?int
    {
        return $this->isonumeric;
    }
    public function setFipscode(?string $fipscode): Country
    {
        $this->fipscode = $fipscode;
        return $this;
    }
    public function getFipscode(): ?string
    {
        return $this->fipscode;
    }
    public function setName(string $name): Country
    {
        $this->name = $name;
        return $this;
    }
    public function getName(): string
    {
        return $this->name;
    }
    public function setCapital(?string $capital): Country
    {
        $this->capital = $capital;
        return $this;
    }
    public function getCapital(): ?string
    {
        return $this->capital;
    }
    public function getAreainsqkm(): ?float
    {
        return $this->areainsqkm;
    }
    public function setAreainsqkm(?float $areainsqkm): Country
    {
        $this->areainsqkm = $areainsqkm;
        return $this;
    }
    public function getPopulation(): ?int
    {
        return $this->population;
    }
    public function setPopulation(?int $population): Country
    {
        $this->population = (int)$population;
        return $this;
    }
    public function getContinent(): ?string
    {
        return $this->continent;
    }
    public function setContinent(?string $continent): Country
    {
        $this->continent = $continent;
        $this->setContinentid($continent);
        return $this;
    }
    public function getContinentid(): ?int
    {
        return $this->continentid;
    }
    public function setContinentid(?string $continentid): Country
    {
        $this->continentid = (array_key_exists($continentid, self::$CONTINENTS))? self::$CONTINENTS[$continentid] : null;
        return $this;
    }
    public function getTld(): ?string
    {
        return $this->tld;
    }
    public function setTld(?string $tld): Country
    {
        $this->tld = $tld;
        return $this;
    }
    public function getCurrency(): ?string
    {
        return $this->currency;
    }
    public function setCurrency(?string $currency): Country
    {
        $this->currency = $currency;
        return $this;
    }
    public function getCurrencyname(): ?string
    {
        return $this->currencyname;
    }
    public function setCurrencyname(?string $currencyname): Country
    {
        $this->currencyname = $currencyname;
        return $this;
    }
    public function getPhone(): ?string
    {
        return $this->phone;
    }
    public function setPhone(?string $phone): Country
    {
        $this->phone = $phone;
        return $this;
    }
    public function getPostalcodeformat(): ?string
    {
        return $this->postalcodeformat;
    }
    public function setPostalcodeformat(?string $postalcodeformat): Country
    {
        $this->postalcodeformat = $postalcodeformat;
        return $this;
    }
    public function getPostalcoderegex(): ?string
    {
        return $this->postalcoderegex;
    }
    public function setPostalcoderegex(?string $postalcoderegex): Country
    {
        $this->postalcoderegex = $postalcoderegex;
        return $this;
    }
    public function getLanguages(): ?string
    {
        return $this->languages;
    }
    public function setLanguages(?string $languages): Country
    {
        $this->languages = $languages;
        return $this;
    }
    public function getNeighbours(): ?string
    {
        return $this->neighbours;
    }
    public function setNeighbours(?string $neighbours): Country
    {
        $this->neighbours = $neighbours;
        return $this;
    }
    public function getEquivalentfipscode(): ?string
    {
        return $this->equivalentfipscode;
    }
    public function setEquivalentfipscode(?string $equivalentfipscode): Country
    {
        $this->equivalentfipscode = $equivalentfipscode;
        return $this;
    }
    public function getGeonameid(): ?int
    {
        return $this->geonameid;
    }
    public function setGeonameid(?int $geonameid): Country
    {
        $this->geonameid = $geonameid;
        return $this;
    }

}
