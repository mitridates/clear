<?php
namespace  App\Domain\Citation\Entity\Trait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Citation date
 */
trait   CitationDateTrait
{
    /**
     * Discoverer month. FD 33
     */
    #[Assert\Range(min: 1, max: 12)]
    #[ORM\Column(name: 'month', type: 'smallint', length: 2, nullable: true, options: ['unsigned' => true, 'fixed' => true])]
    private ?int $month = null;

    /**
     * Discoverer day. FD 32
     */
    #[Assert\Range(min: 1, max: 31)]
    #[ORM\Column(name: 'day', type: 'smallint', length: 2, nullable: true, options: ['unsigned' => true, 'fixed' => true])]
    private ?int $day = null;

    /**
     * Discoverer year. FD 34
     */
    #[Assert\Length(min: 4, max: 4, exactMessage: 'cave.validator.exact.length')]
    #[ORM\Column(name: 'year', type: 'smallint', length: 4, nullable: true, options: ['unsigned' => true, 'fixed' => true])]
    private ?int $year = null;


    public function getMonth(): ?int
    {
        return $this->month;
    }

    public function setMonth(?int $month): self
    {
        $this->month = $month;
        return $this;
    }

    public function getDay(): ?int
    {
        return $this->day;
    }

    public function setDay(?int $day): self
    {
        $this->day = $day;
        return $this;
    }

    public function getYear(): ?int
    {
        return $this->year;
    }
    public function setYear(?int $year): self
    {
        $this->year = $year;
        return $this;
    }
}

