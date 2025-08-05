<?php
namespace  App\Organisation\Domain\Entity;

use App\Domain\Geonames\Entity\{App\Geonames\Domain\Entity\Country};
use App\Fielddefinition\Domain\Entity\Fieldvaluecode;
use App\Geonames\Domain\Entity\Admin1;
use App\Geonames\Domain\Entity\Admin2;
use App\Geonames\Domain\Entity\Admin3;
use App\Shared\Doctrine\Orm\Id\CavernIdGenerator;
use App\Shared\Doctrine\Trait\CrupdatetimeTrait;
use App\Shared\Doctrine\Trait\HiddenTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Organisation
 */
#[ORM\Table(name: 'organisation')]
#[ORM\Index(columns: ['country'], name: 'country_idx')]
#[ORM\Index(columns: ['admin1'], name: 'admin1_idx')]
#[ORM\Index(columns: ['admin2'], name: 'admin2_idx')]
#[ORM\Index(columns: ['admin3'], name: 'admin3_idx')]
#[ORM\Index(columns: ['current_org_id_if_defunct'], name: 'org_ifdefunct_idx')]
#[ORM\Index(columns: ['country_address'], name: 'country_address_idx')]
#[ORM\Index(columns: ['admin1_address'], name: 'admin1_address_idx')]
#[ORM\Index(columns: ['admin2_address'], name: 'admin2_address_idx')]
#[ORM\Index(columns: ['admin3_address'], name: 'admin3_address_idx')]
#[ORM\Index(columns: ['organisation_coverage'], name: 'fieldvaluecode_org_coverage_idx')]
#[ORM\Index(columns: ['organisation_grouping'], name: 'fieldvaluecode_org_grouping_idx')]
#[ORM\Index(columns: ['organisation_type'], name: 'fieldvaluecode_org_type_idx')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class Organisation
{
    use CrupdatetimeTrait, HiddenTrait;


    /**
     * Organisation ID. FD 380
     */
    #[ORM\Column(name: 'id', type: 'string', length: 17, unique: true, nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: CavernIdGenerator::class)]
    protected ?string $id=null;

    /**
     * Can generate registries
     */
    #[ORM\Column(name: 'id_generator', type: 'boolean', nullable: true, options: ['default' => 0])]
    private bool $isgenerator=false;

    /**
     * Organisation code. FD 178
     */
    #[Assert\Length(min: 3, max: 3, exactMessage: 'cave.validator.exact.length')]
    #[Assert\Regex(pattern: '/^[A-Z]{3}$/', message: 'cave.valisator.match.regex', match: true)]
    #[ORM\Column(name: 'organisation_code', type: 'string', length: 3, nullable: true)]
    private ?string $code = null;

    /**
     * Organisation type. FD 381
     */
    #[ORM\ManyToOne(targetEntity: Fieldvaluecode::class)]
    #[ORM\JoinColumn(name: 'organisation_type', referencedColumnName: 'id')]
    private ?Fieldvaluecode $type = null;

    /**
     * Postal code. FD 378
     */
    #[ORM\Column(name: 'postcode', type: 'string', length: 12, nullable: true)]
    private ?string $postcode = null;

    /**
     * Postcode first Y/N. FD 379
     */
    #[ORM\Column(name: 'postcode_first_YN', type: 'boolean', nullable: true)]
    private ?bool $postcodefist = null;

    /**
     * Organisation defunct Y/N. FD 382
     */
    #[ORM\Column(name: 'organisation_defunct_YN', type: 'boolean', nullable: false, options: ['default' => 0])]
    private ?bool $defunct=false;

    /**
     * Final year if defunct. FD 383
     */
    #[Assert\Length(min: 4, max: 4, exactMessage: 'cave.validator.exact.length')]
    #[ORM\Column(name: 'final_year_if_defunct', type: 'smallint', length: 4, nullable: true, options: ['unsigned' => true])]
    private ?int $defunctyear = null;

    /**
     * Current org ID if defunct. FD 384
     */
    #[ORM\OneToOne(targetEntity: Organisation::class)]
    #[ORM\JoinColumn(name: 'current_org_id_if_defunct', referencedColumnName: 'id', unique: false)]
    private ?Organisation $currentidifdefunct = null;

    /**
     * Address line 0. FD 385
     */
    #[ORM\Column(name: 'address_line_0', type: 'string', length: 50, nullable: true)]
    private ?string $addressline0 = null;

    /**
     * Address line 1. FD 386
     */
    #[ORM\Column(name: 'address_line_1', type: 'string', length: 50, nullable: true)]
    private ?string $addressline1 = null;

    /**
     * Address line 2. FD 387
     */
    #[ORM\Column(name: 'address_line_2', type: 'string', length: 50, nullable: true)]
    private ?string $addressline2 = null;

    /**
     *  Address line 3. FD 388
     */
    #[ORM\Column(name: 'address_line_3', type: 'string', length: 50, nullable: true)]
    private ?string $addressline3 = null;

    /**
     * Address line 4. FD 389
     */
    #[ORM\Column(name: 'address_line_4', type: 'string', length: 50, nullable: true)]
    private ?string $addressline4 = null;

    /**
     * Organisation initials. FD 390
     */
    #[ORM\Column(name: 'organisation_initials', type: 'string', length: 8, nullable: true)]
    private ?string $initials = null;

    /**
     * Organisation name. FD 391
     */
    #[Assert\Length(max: 60, maxMessage: 'cave.validator.max.lengt')]
    #[ORM\Column(name: 'organisation_name', type: 'string', length: 60, nullable: false)]
    private ?string $name= null;

    /**
     * Organisation country code. FD 376
     */
    #[ORM\ManyToOne(targetEntity: \App\Geonames\Domain\Entity\Country::class)]
    #[ORM\JoinColumn(name: 'country', referencedColumnName: 'id', nullable: true)]
    private ?\App\Geonames\Domain\Entity\Country $country = null;

    /**
     * State code. n-1 Geonames:Admin1. FD 377
     */
    #[ORM\ManyToOne(targetEntity: Admin1::class)]
    #[ORM\JoinColumn(name: 'admin1', referencedColumnName: 'id', nullable: true)]
    private ?Admin1 $admin1= null;

    /**
     * Province in which an organisation is located. Uncoded
     */
    #[ORM\ManyToOne(targetEntity: Admin2::class)]
    #[ORM\JoinColumn(name: 'admin2', referencedColumnName: 'id', nullable: true)]
    private ?Admin2 $admin2= null;

    /**
     * City / Municipality in which an organisation is located. Uncoded
     */
    #[ORM\ManyToOne(targetEntity: Admin3::class)]
    #[ORM\JoinColumn(name: 'admin3', referencedColumnName: 'id', nullable: true)]
    private ?Admin3 $admin3= null;

    /**
     * Address country code n-1 Country. FD 395
     */
    #[ORM\ManyToOne(targetEntity: \App\Geonames\Domain\Entity\Country::class)]
    #[ORM\JoinColumn(name: 'country_address', referencedColumnName: 'id', nullable: true)]
    private ?\App\Geonames\Domain\Entity\Country $countryaddress = null;

    /**
     * State code. n-1 Geonames:Admin1. State organisation address. Uncoded
     */
    #[ORM\ManyToOne(targetEntity: Admin1::class)]
    #[ORM\JoinColumn(name: 'admin1_address', referencedColumnName: 'id', nullable: true)]
    private ?Admin1 $admin1address = null;

    /**
     * Geonames:Admin2. Admin2 organisation address. Uncoded
     */
    #[ORM\ManyToOne(targetEntity: Admin2::class)]
    #[ORM\JoinColumn(name: 'admin2_address', referencedColumnName: 'id', nullable: true)]
    private ?Admin2 $admin2address = null;

    /**
     * Municipality. n-1 Geonames:Admin3. City organisation address. Uncoded
     */
    #[ORM\ManyToOne(targetEntity: Admin3::class)]
    #[ORM\JoinColumn(name: 'admin3_address', referencedColumnName: 'id', nullable: true)]
    private ?Admin3 $admin3address = null;

    /**
     * Organisation coverage. FD 393
     */
    #[ORM\ManyToOne(targetEntity: Fieldvaluecode::class)]
    #[ORM\JoinColumn(name: 'organisation_coverage', referencedColumnName: 'id', nullable: true)]
    private ?Fieldvaluecode $coverage = null;

    /**
     *  Organisation grouping. FD 394
     */
    #[ORM\ManyToOne(targetEntity: Fieldvaluecode::class)]
    #[ORM\JoinColumn(name: 'organisation_grouping', referencedColumnName: 'id', nullable: true)]
    private ?Fieldvaluecode $grouping = null;

    /**
     * Org contact email address. FD 614
     */
    #[ORM\Column(name: 'organisation_email', type: 'string', length: 50, nullable: true)]
    private ?string $email = null;

    /**
     * Org web page address. FD 615
     */
    #[ORM\Column(name: 'organisation_webpage', type: 'string', length: 80, nullable: true)]
    private ?string $webpage = null;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getIsgenerator(): bool
    {
        return $this->isgenerator;
    }

    public function setIsgenerator(bool $isgenerator): Organisation
    {
        $this->isgenerator = $isgenerator;
        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): Organisation
    {
        $this->code = $code;
        return $this;
    }

    public function getType(): ?Fieldvaluecode
    {
        return $this->type;
    }
    
    public function setType(?Fieldvaluecode $type): Organisation
    {
        $this->type = $type;
        return $this;
    }

    public function getPostcode(): ?string
    {
        return $this->postcode;
    }

    public function setPostcode(?string $postcode): Organisation
    {
        $this->postcode = $postcode;
        return $this;
    }

    public function getPostcodefist(): ?bool
    {
        return $this->postcodefist;
    }

    public function setPostcodefist(?bool $postcodefist): Organisation
    {
        $this->postcodefist = $postcodefist;
        return $this;
    }

    public function getDefunct(): bool
    {
        return $this->defunct;
    }

    public function setDefunct(bool $defunct): Organisation
    {
        $this->defunct = $defunct;
        return $this;
    }

    public function getDefunctyear(): ?int
    {
        return $this->defunctyear;
    }

    public function setDefunctyear(?int $defunctyear): Organisation
    {
        $this->defunctyear = $defunctyear;
        return $this;
    }

    public function getCurrentidifdefunct(): ?Organisation
    {
        return $this->currentidifdefunct;
    }

    public function setCurrentidifdefunct(?Organisation $currentidifdefunct): Organisation
    {
        $this->currentidifdefunct = $currentidifdefunct;
        return $this;
    }

    public function getAddressline0(): ?string
    {
        return $this->addressline0;
    }

    public function setAddressline0(?string $addressline0): Organisation
    {
        $this->addressline0 = $addressline0;
        return $this;
    }

    public function getAddressline1(): ?string
    {
        return $this->addressline1;
    }

    public function setAddressline1(?string $addressline1): Organisation
    {
        $this->addressline1 = $addressline1;
        return $this;
    }

    public function getAddressline2(): ?string
    {
        return $this->addressline2;
    }

    public function setAddressline2(?string $addressline2): Organisation
    {
        $this->addressline2 = $addressline2;
        return $this;
    }

    public function getAddressline3(): ?string
    {
        return $this->addressline3;
    }

    public function setAddressline3(?string $addressline3): Organisation
    {
        $this->addressline3 = $addressline3;
        return $this;
    }

    public function getAddressline4(): ?string
    {
        return $this->addressline4;
    }

    public function setAddressline4(?string $addressline4): Organisation
    {
        $this->addressline4 = $addressline4;
        return $this;
    }

    public function getInitials(): ?string
    {
        return $this->initials;
    }

    public function setInitials(?string $initials): Organisation
    {
        $this->initials = $initials;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): Organisation
    {
        $this->name = $name;
        return $this;
    }

    public function getCountry(): ?\App\Geonames\Domain\Entity\Country
    {
        return $this->country;
    }

    public function setCountry(?\App\Geonames\Domain\Entity\Country $country): Organisation
    {
        $this->country = $country;
        return $this;
    }

    public function getAdmin1(): ?Admin1
    {
        return $this->admin1;
    }

    public function setAdmin1(?Admin1 $admin1): Organisation
    {
        $this->admin1 = $admin1;
        return $this;
    }

    public function getAdmin2(): ?Admin2
    {
        return $this->admin2;
    }

    public function setAdmin2(?Admin2 $admin2): Organisation
    {
        $this->admin2 = $admin2;
        return $this;
    }

    public function getAdmin3(): ?Admin3
    {
        return $this->admin3;
    }

    public function setAdmin3(?Admin3 $admin3): Organisation
    {
        $this->admin3 = $admin3;
        return $this;
    }

    public function getCountryaddress(): ?\App\Geonames\Domain\Entity\Country
    {
        return $this->countryaddress;
    }

    public function setCountryaddress(?\App\Geonames\Domain\Entity\Country $countryaddress): Organisation
    {
        $this->countryaddress = $countryaddress;
        return $this;
    }

    public function getAdmin1address(): ?Admin1
    {
        return $this->admin1address;
    }

    public function setAdmin1address(?Admin1 $admin1address): Organisation
    {
        $this->admin1address = $admin1address;
        return $this;
    }

    public function getAdmin2address(): ?Admin2
    {
        return $this->admin2address;
    }

    public function setAdmin2address(?Admin2 $admin2address): Organisation
    {
        $this->admin2address = $admin2address;
        return $this;
    }

    public function getAdmin3address(): ?Admin3
    {
        return $this->admin3address;
    }

    public function setAdmin3address(?Admin3 $admin3address): Organisation
    {
        $this->admin3address = $admin3address;
        return $this;
    }

    public function getCoverage(): ?Fieldvaluecode
    {
        return $this->coverage;
    }

    public function setCoverage(?Fieldvaluecode $coverage): Organisation
    {
        $this->coverage = $coverage;
        return $this;
    }

    public function getGrouping(): ?Fieldvaluecode
    {
        return $this->grouping;
    }

    public function setGrouping(?Fieldvaluecode $grouping): Organisation
    {
        $this->grouping = $grouping;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): Organisation
    {
        $this->email = $email;
        return $this;
    }

    public function getWebpage(): ?string
    {
        return $this->webpage;
    }

    public function setWebpage(?string $webpage): Organisation
    {
        $this->webpage = $webpage;
        return $this;
    }

}
