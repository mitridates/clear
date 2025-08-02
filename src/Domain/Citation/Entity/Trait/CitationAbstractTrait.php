<?php
namespace  App\Domain\Citation\Entity\Trait;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Citation date
 */
trait   CitationAbstractTrait
{
        /**
         * @var ?int Local abstract year. FD 323
         * @Assert\Length(
         *      min = 4,
         *      max = 4,
         *      exactMessage= "cave.validator.exact.length"
         * )
         * @ORM\Column(name="local_abstract_year", type="smallint", length=4, nullable=true, options={"unsigned"=true, "fixed" = true})
         */
        private ?int $abstractyear;

        /**
         * Type of publication. FD 324 (value code ???)
         * @Assert\Length(
         *      max = 1,
         *      maxMessage= "cave.validator.max.length"
         * )
         *
         * @ORM\Column(name="local_abstract_category", type="string", length=1, nullable=true, options={"fixed" = true})
         */
        private ?string $abstractcategory;

        /**
         * @return ?int
         */
        public function getAbstractyear(): ?int
        {
            return $this->abstractyear;
        }
        /**
         * Local abstract reference. FD 325 ???
         * @Assert\Length(
         *      max = 4,
         *      maxMessage= "cave.validator.max.length"
         * )
         * @ORM\Column(name="local_abstract_referencee", type="string", length=4, nullable=true)
         */
        private $abstractreference;
        /**
         * @param ?int $abstractyear
         */
        public function setAbstractyear(?int $abstractyear): self
        {
            $this->abstractyear = $abstractyear;
            return $this;
        }

        /**
         * @return ?string
         */
        public function getAbstractcategory(): ?string
        {
            return $this->abstractcategory;
        }

        /**
         * @param ?string $abstractcategory
         */
        public function setAbstractcategory(?string $abstractcategory): self
        {
            $this->abstractcategory = $abstractcategory;
            return $this;
        }

        /**
         * @return ?string
         */
        public function getAbstractreference(): ?string
        {
            return $this->abstractreference;
        }

        /**
         * @param ?string $abstractreference
         */
        public function setAbstractreference(?string $abstractreference): self
        {
            $this->abstractreference = $abstractreference;
            return $this;
        }


}

