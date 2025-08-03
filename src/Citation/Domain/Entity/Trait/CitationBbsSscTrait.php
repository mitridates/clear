<?php
namespace  App\Citation\Domain\Entity\Trait;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Citation date
 */
trait   CitationBbsSscTrait
{

        /**
         * @var ?int BBS year. FD 326
         * @Assert\Length(
         *      min = 4,
         *      max = 4,
         *      exactMessage= "cave.validator.exact.length"
         * )
         * @ORM\Column(name="bbs_year", type="smallint", length=4, nullable=true, options={"unsigned"=true, "fixed" = true})
         */
        private $bbsyear;
    
        /**
         * BBS sequence number. FD 327
         * @Assert\Length(
         *      max = 5,
         *      maxMessage= "cave.validator.max.length"
         * )
         * @ORM\Column(name="bbs_sequence_number", type="string", length=5, nullable=true)
         */
        private $bbssequencenumber;
    
        /**
         * @var ?int SSC cod. FD 328 ???
         * @Assert\Length(
         *      max = 3,
         *      maxMessage= "cave.validator.max.length"
         * )
         * @ORM\Column(name="ssc_code", type="string", length=3, nullable=true, options={"fixed" = true})
         */
        private $ssccode;
    
    
        /**
         * @return ?int
         */
        public function getBbsyear(): ?int
        {
            return $this->bbsyear;
        }
    
        /**
         * @param ?int $bbsyear
         */
        public function setBbsyear(?int $bbsyear): self
        {
            $this->bbsyear = $bbsyear;
            return $this;
        }
    
        /**
         * @return ?string
         */
        public function getBbssequencenumber(): ?string
        {
            return $this->bbssequencenumber;
        }
    
        /**
         * @param ?string $bbssequencenumber
         */
        public function setBbssequencenumber(?string $bbssequencenumber): self
        {
            $this->bbssequencenumber = $bbssequencenumber;
            return $this;
        }
    
        /**
         * @return ?int
         */
        public function getSsccode(): ?int
        {
            return $this->ssccode;
        }
    
        /**
         * @param ?int $ssccode
         */
        public function setSsccode(?int $ssccode): self
        {
            $this->ssccode = $ssccode;
            return $this;
        }
}

