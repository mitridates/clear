<?php
namespace  App\Domain\Person\Entity;
use App\Domain\Geonames\Entity\{Country};
use App\Domain\Geonames\Entity\Admin1;
use App\Domain\Geonames\Entity\Admin2;
use App\Domain\Geonames\Entity\Admin3;
use App\Domain\Organisation\Entity\Organisation;
use App\Shared\Doctrine\Orm\Id\CavernIdGenerator;
use CrupdatetimeTrait;
use Doctrine\ORM\Mapping as ORM;
use HiddenTrait;

;

/**
 * Person
 *
 * @link http://kid.caves.org.au/kid/doc/table_relationships?entity=PE
 */
#[ORM\Table(name: 'person')]
#[ORM\Index(columns: ['country'], name: 'country_idx')]
#[ORM\Index(columns: ['admin1'], name: 'admin1_idx')]
#[ORM\Index(columns: ['admin2'], name: 'admin2_idx')]
#[ORM\Index(columns: ['admin3'], name: 'admin3_idx')]
#[ORM\Index(columns: ['organisation'], name: 'organisation_idx')]
#[ORM\Index(columns: ['organisation2'], name: 'organisation2_idx')]
#[ORM\Index(columns: ['organisation3'], name: 'organisation3_idx')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class Person
{
    use HiddenTrait, CrupdatetimeTrait;
    /**
     * Person ID. FD 478
     */
    #[ORM\Column(name: 'id', type: 'string', length: 17, nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: CavernIdGenerator::class)]
    private ?string $id=null;

    /**
     * Surname. FD 479
     */
    #[ORM\Column(name: 'surname', type: 'string', length: 30, nullable: true)]
    private ?string $surname = null;

    /**
     * Usual first name. FD 480
     */
    #[ORM\Column(name: 'usual_first_name', type: 'string', length: 20, nullable: true)]
    private ?string $name = null;

    /**
     * Middle initial. FD 481
     */
    #[ORM\Column(name: 'middle_initial', type: 'string', length: 5, nullable: true)]
    private ?string $middleinitial = null;

    /**
     * Initials for given names. FD 482
     */
    #[ORM\Column(name: 'initials_for_given_names', type: 'string', length: 10, nullable: true)]
    private ?string $initialforgivennames = null;

    /**
     * Title. FD 483
     */
    #[ORM\Column(name: 'title', type: 'string', length: 20, nullable: true)]
    private ?string $title = null;

    /**
     * M: male, F: Female.
     * Gender Male/Female/undefined. FD uncoded
     */
    #[ORM\Column(name: 'gender', type: 'boolean', nullable: true, options: ['default' => null])]
    private ?bool $gender = null;

    /**
     * @var ?Country FD 493
     */
    #[ORM\ManyToOne(targetEntity: Country::class)]
    #[ORM\JoinColumn(name: 'country', referencedColumnName: 'id', nullable: true)]
    private ?Country $country = null;

    /**
     * @var ?Admin1 State. FD 490
     */
    #[ORM\ManyToOne(targetEntity: Admin1::class)]
    #[ORM\JoinColumn(name: 'admin1', referencedColumnName: 'id', nullable: true)]
    private ?Admin1 $admin1 = null;
    /**
      * @var ?Admin2  Province. FD uncoded
      **/
     #[ORM\ManyToOne(targetEntity: Admin2::class)]
     #[ORM\JoinColumn(name: 'admin2', referencedColumnName: 'id', nullable: true)]
     private ?Admin2 $admin2 = null;

    /**
     * @var ?Admin3 Municipality. FD uncoded
     **/
    #[ORM\ManyToOne(targetEntity: Admin3::class)]
    #[ORM\JoinColumn(name: 'admin3', referencedColumnName: 'id', nullable: true)]
    private ?Admin3 $admin3 = null;

    /**
     *  City or suburb. FD 488
     */
    #[ORM\Column(name: 'city_or_suburb', type: 'string', length: 25, nullable: true)]
    private ?string $cityorsuburb = null;

    /**
     * Address line 1. FD 484
     */
    #[ORM\Column(name: 'address_line_1', type: 'string', length: 50, nullable: true)]
    private ?string $addressline1 = null;

    /**
     * Address line 2. FD 485
     */
    #[ORM\Column(name: 'address_line_2', type: 'string', length: 50, nullable: true)]
    private ?string $addressline2 = null;

    /**
     * Address line 3. FD 486
     */
    #[ORM\Column(name: 'address_line_3', type: 'string', length: 50, nullable: true)]
    private ?string $addressline3 = null;

    /**
     * Address line 4. FD 487
     */
    #[ORM\Column(name: 'address_line_4', type: 'string', length: 50, nullable: true)]
    private ?string $addressline4 = null;

    /**
     * Postcode. FD 491
     */
    #[ORM\Column(name: 'postcode', type: 'string', length: 12, nullable: true)]
    private ?string $postcode = null;

    /**
     * Email address. FD 494
     */
    #[ORM\Column(name: 'email_address', type: 'string', length: 50, nullable: true)]
    private ?string $email = null;

    /**
     * Phone numbers prefix. FD 495
     */
    #[ORM\Column(name: 'phone_numbers_prefix', type: 'string', length: 15, nullable: true)]
    private ?string $phoneprefix = null;

    /**
     * Home phone number. FD 496
     */
    #[ORM\Column(name: 'home_phone_number', type: 'string', length: 18, nullable: true)]
    private ?string $homephonenumber = null;

    /**
     * Work phone number. FD 497
     */
    #[ORM\Column(name: 'work_phone_number', type: 'string', length: 18, nullable: true)]
    private ?string $workphonenumber = null;

    /**
     * Mobile phone number. FD 498
     */
    #[ORM\Column(name: 'mobile_phone_number', type: 'string', length: 18, nullable: true)]
    private ?string $mobilephonenumber = null;

    /**
     * Fax phone number. FD 499
     */
    #[ORM\Column(name: 'fax_phone_number', type: 'string', length: 18, nullable: true)]
    private ?string $faxphonenumber = null;

    /**
     * Pager phone user number. FD 500
     */
    #[ORM\Column(name: 'pager_phone_user_number', type: 'string', length: 18, nullable: true)]
    private ?string $pagerphonenumber = null;

    /**
     * FD 501
     */
    #[ORM\ManyToOne(targetEntity: Organisation::class)]
    #[ORM\JoinColumn(name: 'organisation', referencedColumnName: 'id')]
    private ?Organisation $organisation = null;

    /**
     *FD 502
     */
    #[ORM\ManyToOne(targetEntity: Organisation::class)]
    #[ORM\JoinColumn(name: 'organisation2', referencedColumnName: 'id')]
    private ?Organisation $organisation2 = null;

    /**
     *
     *FD 503
     */
    #[ORM\ManyToOne(targetEntity: Organisation::class)]
    #[ORM\JoinColumn(name: 'organisation3', referencedColumnName: 'id')]
    private ?Organisation $organisation3 = null;

    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * Formated name
     * @example
     *      $person->rname('{surname}, {name}');//Montoya, Íñigo
     *      $person->rname('{name} {middleinitial} {surname}')// Íñigo L Montoya
     */
    public function curlyName(string $text): string
    {
        if(preg_match_all('/{+(.*?)}/', $text, $matches))
        {
            foreach ($matches[0] as $k=>$regw)//['{name}', '{surname}']
            {
                $w= $matches[1][$k];//['name', 'surname']
                if(property_exists($this, $w)){
                    $text = str_replace($regw, $this->$w, $text);
                }
            }
        }
        return $text;
    }

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(?string $surname): Person
    {
        $this->surname = $surname;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): Person
    {
        $this->name = $name;
        return $this;
    }

    public function getMiddleinitial(): ?string
    {
        return $this->middleinitial;
    }

    public function setMiddleinitial(?string $middleinitial): Person
    {
        $this->middleinitial = $middleinitial;
        return $this;
    }

    public function getInitialforgivennames(): ?string
    {
        return $this->initialforgivennames;
    }

    public function setInitialforgivennames(?string $initialforgivennames): Person
    {
        $this->initialforgivennames = $initialforgivennames;
        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): Person
    {
        $this->title = $title;
        return $this;
    }

    public function getGender(): ?bool
    {
        return $this->gender;
    }

    public function setGender(?bool $gender): Person
    {
        $this->gender = $gender;
        return $this;
    }

    public function getCountry(): ?Country
    {
        return $this->country;
    }

    public function setCountry(?Country $country): Person
    {
        $this->country = $country;
        return $this;
    }

    public function getAdmin1(): ?Admin1
    {
        return $this->admin1;
    }

    public function setAdmin1(?Admin1 $admin1): Person
    {
        $this->admin1 = $admin1;
        return $this;
    }

    public function getAdmin2(): ?Admin2
    {
        return $this->admin2;
    }

    public function setAdmin2(?Admin2 $admin2): Person
    {
        $this->admin2 = $admin2;
        return $this;
    }

    public function getAdmin3(): ?Admin3
    {
        return $this->admin3;
    }

    public function setAdmin3(?Admin3 $admin3): Person
    {
        $this->admin3 = $admin3;
        return $this;
    }

    public function getCityorsuburb(): ?string
    {
        return $this->cityorsuburb;
    }

    public function setCityorsuburb(?string $cityorsuburb): Person
    {
        $this->cityorsuburb = $cityorsuburb;
        return $this;
    }

    public function getAddressline1(): ?string
    {
        return $this->addressline1;
    }

    public function setAddressline1(?string $addressline1): Person
    {
        $this->addressline1 = $addressline1;
        return $this;
    }

    public function getAddressline2(): ?string
    {
        return $this->addressline2;
    }

    public function setAddressline2(?string $addressline2): Person
    {
        $this->addressline2 = $addressline2;
        return $this;
    }

    public function getAddressline3(): ?string
    {
        return $this->addressline3;
    }

    public function setAddressline3(?string $addressline3): Person
    {
        $this->addressline3 = $addressline3;
        return $this;
    }

    public function getAddressline4(): ?string
    {
        return $this->addressline4;
    }

    public function setAddressline4(?string $addressline4): Person
    {
        $this->addressline4 = $addressline4;
        return $this;
    }

    public function getPostcode(): ?string
    {
        return $this->postcode;
    }

    public function setPostcode(?string $postcode): Person
    {
        $this->postcode = $postcode;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): Person
    {
        $this->email = $email;
        return $this;
    }

    public function getPhoneprefix(): ?string
    {
        return $this->phoneprefix;
    }

    public function setPhoneprefix(?string $phoneprefix): Person
    {
        $this->phoneprefix = $phoneprefix;
        return $this;
    }

    public function getHomephonenumber(): ?string
    {
        return $this->homephonenumber;
    }

    public function setHomephonenumber(?string $homephonenumber): Person
    {
        $this->homephonenumber = $homephonenumber;
        return $this;
    }

    public function getWorkphonenumber(): ?string
    {
        return $this->workphonenumber;
    }

    public function setWorkphonenumber(?string $workphonenumber): Person
    {
        $this->workphonenumber = $workphonenumber;
        return $this;
    }

    public function getMobilephonenumber(): ?string
    {
        return $this->mobilephonenumber;
    }

    public function setMobilephonenumber(?string $mobilephonenumber): Person
    {
        $this->mobilephonenumber = $mobilephonenumber;
        return $this;
    }

    public function getFaxphonenumber(): ?string
    {
        return $this->faxphonenumber;
    }

    public function setFaxphonenumber(?string $faxphonenumber): Person
    {
        $this->faxphonenumber = $faxphonenumber;
        return $this;
    }

    public function getPagerphonenumber(): ?string
    {
        return $this->pagerphonenumber;
    }

    public function setPagerphonenumber(?string $pagerphonenumber): Person
    {
        $this->pagerphonenumber = $pagerphonenumber;
        return $this;
    }

    public function getOrganisation(): ?Organisation
    {
        return $this->organisation;
    }

    public function setOrganisation(?Organisation $organisation): Person
    {
        $this->organisation = $organisation;
        return $this;
    }

    public function getOrganisation2(): ?Organisation
    {
        return $this->organisation2;
    }

    public function setOrganisation2(?Organisation $organisation2): Person
    {
        $this->organisation2 = $organisation2;
        return $this;
    }

    public function getOrganisation3(): ?Organisation
    {
        return $this->organisation3;
    }

    public function setOrganisation3(?Organisation $organisation3): Person
    {
        $this->organisation3 = $organisation3;
        return $this;
    }
}
