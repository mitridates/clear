<?php

namespace App\Geonames\Domain\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'geonames_geonames')]
#[ORM\Entity]
class Geonames
{
    /**
     * @var integer Geonameid
     */
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    #[ORM\Column(name: 'id', type: 'integer')]
    private int $id;

    /**
     * Name of geographical point (utf8) varchar(200)
     */
    #[ORM\Column(name: 'name', type: 'string', length: 200, nullable: false)]
    private ?string $name = null;

    /**
     * Name of geographical point in plain ascii characters, varchar(200)
     */
    #[ORM\Column(name: 'asciiname', type: 'string', length: 200, nullable: true)]
    private ?string $asciiname = null;

    #[ORM\Column(name: 'latitude', type: 'decimal', precision: 10, scale: 7, nullable: true)]
    private ?float $latitude = null;

    #[ORM\Column(name: 'longitude', type: 'decimal', precision: 10, scale: 7, nullable: true)]
    private ?float $longitude = null;

    /**
     * feature class
     */
    #[ORM\Column(name: 'featureclass', type: 'string', length: 1, nullable: true)]
    private ?string $featureclass = null;

    /**
     * feature code
     */
    #[ORM\Column(name: 'featurecode', type: 'string', length: 8, nullable: true)]
    private ?string $featurecode = null;

    /**
     * Country cc2
     */
    #[ORM\Column(name: 'country_id', type: 'string', length: 2, nullable: true)]
    private ?string $country = null;

    /**
     * Alternate country codes, comma separated, ISO-3166 2-letter country code (cc2)
     */
    #[ORM\Column(name: 'cc2', type: 'string', length: 60, nullable: true)]
    private ?string $cc2 = null;

    /**
     * ADM1
     */
    #[ORM\Column(name: 'admin1_id', type: 'string', length: 20, nullable: true)]
    private ?string $admin1 = null;

    /**
     * ADM2
     */
    #[ORM\Column(name: 'admin2_id', type: 'string', length: 25, nullable: true)]
    private ?string $admin2 = null;

    /**
     * ADM3
     */
    #[ORM\Column(name: 'admin3_id', type: 'string', length: 30, nullable: true)]
    private ?string $admin3 = null;

    /**
     * ADM4
     */
    #[ORM\Column(name: 'admin4_id', type: 'string', length: 40, nullable: true)]
    private ?string $admin4 = null;

    #[ORM\Column(name: 'population', type: 'integer', nullable: true)]
    private ?int $population = null;

    #[ORM\Column(name: 'elevation', type: 'integer', nullable: true)]
    private ?int $elevation = null;

    /**
     * Digital elevation model, srtm3 or gtopo30, average elevation of 3''x3'' (ca 90mx90m)
     *      or 30''x30'' (ca 900mx900m) area in meters, integer. srtm processed by cgiar/ciat.
     */
    #[ORM\Column(name: 'dem', type: 'integer', nullable: true)]
    private ?int $dem = null;

    #[ORM\ManyToOne(targetEntity: 'Timezone')]
    #[ORM\Column(name: 'timezone_id', type: 'string', length: 200, nullable: true)]
    private ?string $timezone = null;

    /**
     * comma separated, ascii names
     */
    #[ORM\Column(name: 'alternatenames', type: 'text', nullable: true)]
    private ?string $alternatenames = null;

    #[ORM\Column(name: 'modificationdate', type: 'date', nullable: true)]
    private ?DateTime $modificationdate = null;

    public function getId(): int
    {
        return $this->id;
    }

    public function setName(?string $name):Geonames
    {
        $this->name = $name;
        return $this;
    }

    public function getName():?string
    {
        return $this->name;
    }

    public function setAsciiname(?string $asciiname): Geonames
    {
        $this->asciiname = $asciiname;
        return $this;
    }

    public function getAsciiname():?string
    {
        return $this->asciiname;
    }

    public function setAlternatenames(?string $alternatenames): Geonames
    {
        $this->alternatenames = $alternatenames;
        return $this;
    }

    public function getAlternatenames():?string
    {
        return $this->alternatenames;
    }

    public function setLatitude(?float $latitude):Geonames
    {
        $this->latitude = $latitude;
        return $this;
    }

    public function getLatitude():?float
    {
        return $this->latitude;
    }

    public function setLongitude(?float $longitude): Geonames
    {
        $this->longitude = $longitude;
        return $this;
    }

    public function getLongitude():?float
    {
        return $this->longitude;
    }

    public function setFeatureclass(?string $featureclass): Geonames
    {
        $this->featureclass = $featureclass;
        return $this;
    }

    public function getFeatureclass():?string
    {
        return $this->featureclass;
    }

    public function setFeaturecode(?string $featurecode): Geonames
    {
        $this->featurecode = $featurecode;
        return $this;
    }

    public function getFeaturecode():?string
    {
        return $this->featurecode;
    }

    public function setCountry(?string $country): Geonames
    {
        $this->country = $country;
        return $this;
    }

    public function getCountry():?string
    {
        return $this->country;
    }

    public function setCc2(?string $cc2): Geonames
    {
        $this->cc2 = $cc2;
        return $this;
    }

    public function getCc2():?string
    {
        return $this->cc2;
    }

    public function setAdmin1(?string $admin1): Geonames
    {
        $this->admin1 = $admin1;
        return $this;
    }

    public function getAdmin1():?string
    {
        return $this->admin1;
    }

    public function setAdmin2(?string $admin2): Geonames
    {
        $this->admin2 = $admin2;
        return $this;
    }

    public function getAdmin2():?string
    {
        return $this->admin2;
    }

    public function setAdmin3(?string $admin3): Geonames
    {
        $this->admin3 = $admin3;
        return $this;
    }

    public function getAdmin3():?string
    {
        return $this->admin3;
    }

    public function setAdmin4(?string $admin4): Geonames
    {
        $this->admin4 = $admin4;
        return $this;
    }

    public function getAdmin4():?string
    {
        return $this->admin4;
    }

    public function setPopulation(?int $population): Geonames
    {
        $this->population = (int)$population;
        return $this;
    }

    public function getPopulation():?int
    {
        return $this->population;
    }

    public function setElevation(?int $elevation): Geonames
    {
        $this->elevation = (int)$elevation;
        return $this;
    }

    public function getElevation():?int
    {
        return $this->elevation;
    }

    public function setDem(?int $dem): Geonames
    {
        $this->dem = (int)$dem;
        return $this;
    }

    public function getDem():?int
    {
        return $this->dem;
    }

    public function setTimezone(?string $timezone): Geonames
    {
        $this->timezone = $timezone;
        return $this;
    }

    public function getTimezone():?string
    {
        return $this->timezone;
    }

    public function setModificationdate(DateTime $modificationdate): Geonames
    {
        $this->modificationdate = $modificationdate;
        return $this;
    }

    public function getModificationdate():?DateTime
    {
        return $this->modificationdate;
    }

}
