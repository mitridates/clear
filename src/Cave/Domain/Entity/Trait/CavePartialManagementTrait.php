<?php
namespace  App\Cave\Domain\Entity\Trait;
use App\Cave\Domain\Entity\Cave;
use App\Fielddefinition\Domain\Entity\{Fieldvaluecode};
use App\Organisation\Domain\Entity\Organisation;
use App\Person\Domain\Entity\Person;
use Doctrine\ORM\Mapping as ORM;

trait CavePartialManagementTrait
{
    /**
     * FD 42
     */
    #[ORM\ManyToOne(targetEntity: Fieldvaluecode::class)]
    #[ORM\JoinColumn(name: 'owner_type', referencedColumnName: 'id')]
    private ?Fieldvaluecode $ownertype;

    /**
     * FD 44
     */
    #[ORM\Column(name: 'management_classifier', type: 'string', length: 8, nullable: true)]
    private ?string $managementclassifier;

    /**
     * FD 45
     */
    #[ORM\ManyToOne(targetEntity: Fieldvaluecode::class)]
    #[ORM\JoinColumn(name: 'management_category', referencedColumnName: 'id', nullable: true)]
    private ?Fieldvaluecode $managementcategory;

    /**
     * @var ?Person FD 228
     */
    #[ORM\ManyToOne(targetEntity: 'App\Entity\Person')]
    #[ORM\JoinColumn(name: 'owner_person_id', referencedColumnName: 'id', nullable: true)]
    private ?Person $ownerperson;

    /**
     * @var ?Organisation  FD 417
     */
    #[ORM\ManyToOne(targetEntity: 'App\Entity\Organisation')]
    #[ORM\JoinColumn(name: 'owner_organisation_id', referencedColumnName: 'id', nullable: true)]
    private ?Organisation $ownerorganisation;

    /**
     * FD 418
     */
    #[ORM\Column(name: 'owner_name', type: 'string', length: 60, nullable: true)]
    private ?string $ownername;

    /**
     * FD 47
     */
    #[ORM\Column(name: 'controller', type: 'string', length: 8, nullable: true)]
    private ?string $controller;

    public function getOwnertype(): ?Fieldvaluecode
    {
        return $this->ownertype;
    }

    public function setOwnertype(?Fieldvaluecode $ownertype): Cave
    {
        $this->ownertype = $ownertype;
        return $this;
    }

    public function getManagementclassifier(): ?string
    {
        return $this->managementclassifier;
    }

    public function setManagementclassifier(?string $managementclassifier): Cave
    {
        $this->managementclassifier = $managementclassifier;
        return $this;
    }

    public function getManagementcategory(): ?Fieldvaluecode
    {
        return $this->managementcategory;
    }

    public function setManagementcategory(?Fieldvaluecode $managementcategory): Cave
    {
        $this->managementcategory = $managementcategory;
        return $this;
    }

    public function getOwnerperson(): ?Person
    {
        return $this->ownerperson;
    }

    public function setOwnerperson(?Person $ownerperson): Cave
    {
        $this->ownerperson = $ownerperson;
        return $this;
    }

    public function getOwnerorganisation(): ?Organisation
    {
        return $this->ownerorganisation;
    }

    public function setOwnerorganisation(?Organisation $ownerorganisation): Cave
    {
        $this->ownerorganisation = $ownerorganisation;
        return $this;
    }

    public function getOwnername(): ?string
    {
        return $this->ownername;
    }

    public function setOwnername(?string $ownername): Cave
    {
        $this->ownername = $ownername;
        return $this;
    }

    public function getController(): ?string
    {
        return $this->controller;
    }

    public function setController(?string $controller): Cave
    {
        $this->controller = $controller;
        return $this;
    }
}