<?php
namespace  App\Entity\Citation\Trait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Citation date
 */
trait   CitationAccessDateTrait
{
    /**
     * Discoverer accessmonth. FD 33
     */
    #[Assert\Range(min: 1, max: 12)]
    #[ORM\Column(name: 'access_month', type: 'smallint', length: 2, nullable: true, options: ['unsigned' => true, 'fixed' => true])]
    private ?int $accessmonth = null;

    /**
     * Discoverer accessday. FD 32
     */
    #[Assert\Range(min: 1, max: 31)]
    #[ORM\Column(name: 'access_day', type: 'smallint', length: 2, nullable: true, options: ['unsigned' => true, 'fixed' => true])]
    private ?int $accessday = null;

    /**
     * Discoverer accessyear. FD 34
     */
    #[Assert\Length(min: 4, max: 4, exactMessage: 'cave.validator.exact.length')]
    #[ORM\Column(name: 'access_year', type: 'smallint', length: 4, nullable: true, options: ['unsigned' => true, 'fixed' => true])]
    private ?int $accessyear = null;



    public function getAccessmonth(): ?int
    {
        return $this->accessmonth;
    }

    public function setAccessmonth(?int $accessmonth): self
    {
        $this->accessmonth = $accessmonth;
        return $this;
    }

    public function getAccessaccessday(): ?int
    {
        return $this->accessday;
    }

    public function setAccessaccessday(?int $accessday): self
    {
        $this->accessday = $accessday;
        return $this;
    }

    public function getAccessyear(): ?int
    {
        return $this->accessyear;
    }
    public function setAccessyear(?int $accessyear): self
    {
        $this->accessyear = $accessyear;
        return $this;
    }
}

