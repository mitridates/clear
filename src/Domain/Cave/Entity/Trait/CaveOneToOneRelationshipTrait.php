<?php
namespace  App\Domain\Cave\Entity\Trait;
use App\Domain\Cave\Entity\Cavedescription;
use App\Domain\Cave\Entity\Caveentrancememo;
use App\Domain\Cave\Entity\Cavehistory;
use App\Domain\Cave\Entity\Cavehowtofind;
use App\Domain\Cave\Entity\Caveposition;
use Doctrine\ORM\Mapping as ORM;

//use  App\Entity\Cave\Caveenvironment;
//use  App\Entity\Cave\Cavemanagement;
//use  App\Entity\Cave\Cavemeasure;

trait CaveOneToOneRelationshipTrait
{
    /**
     * CA0530 Description of the cave
     */
    #[ORM\OneToOne(mappedBy: 'cave', targetEntity: Cavedescription::class, cascade: ['persist', 'remove'])]
    private ?Cavedescription $cavedescription;

    /**
     * CA0534 Entrance description (memo)
     */
    #[ORM\OneToOne(mappedBy: 'cave', targetEntity: Caveentrancememo::class, cascade: ['persist', 'remove'])]
    private ?Caveentrancememo $caveentrancememo;

//    /**
    //     * @var Caveenvironment CA0000 Partial cave.
    //     * @ORM\OneToOne(
    //     *           targetEntity=" App\Entity\Cave\Caveenvironment",
    //     *           mappedBy="cave",
    //     *           cascade={"persist", "remove"}
    //     * )
    //     */
    //    private $caveenvironment;
    /**
     * History of cave (uncoded)
     */
    #[ORM\OneToOne(mappedBy: 'cave', targetEntity: Cavehistory::class, cascade: ['persist', 'remove'])]
    private ?Cavehistory $cavehistory;

    #[ORM\OneToOne(mappedBy: 'cave', targetEntity: Cavehowtofind::class, cascade: ['persist', 'remove'])]
    private ?Cavehowtofind $cavehowtofind;

//    /**
    //     * @var  Cavedimension CA0000 Partial cave.
    //     * @ORM\OneToOne(
    //     *           targetEntity=" App\Entity\Cave\Cavedimension",
    //     *           mappedBy="cave",
    //     *           cascade={"persist", "remove"}
    //     * )
    //     */
    //    private $cavedimension;
    //    /**
    //     * @var Cavemanagement CA0000 Partial cave.
    //     * @ORM\OneToOne(
    //     *           targetEntity=" App\Entity\Cave\Cavemanagement",
    //     *           mappedBy="cave",
    //     *           cascade={"persist", "remove"}
    //     * )
    //     */
    //    private $cavemanagement;
    /**
     * CA0245 Exact position.
     */
    #[ORM\OneToOne(mappedBy: 'cave', targetEntity: Caveposition::class, cascade: ['persist', 'remove'])]
    private ?Caveposition $caveposition;

    public function getCavedescription(): ?Cavedescription
    {
        return $this->cavedescription;
    }

    public function getCaveentrancememo(): ?Caveentrancememo
    {
        return $this->caveentrancememo;
    }

//    /**
//     * @return ?Caveenvironment
//     */
//    public function getCaveenvironment()
//    {
//        return $this->caveenvironment;
//    }

    public function getCavehistory(): ?Cavehistory
    {
        return $this->cavehistory;
    }

    public function getCavehowtofind(): ?Cavehowtofind
    {
        return $this->cavehowtofind;
    }
//
//    /**
//     * @return ?Cavedimension
//     */
//    public function getCavedimension(): ?Cavedimension
//    {
//        return $this->cavedimension;
//    }
//
//    /**
//     * @return ?Cavemanagement
//     */
//    public function getCavemanagement(): ?Cavemanagement
//    {
//        return $this->cavemanagement;
//    }

    public function getCaveposition(): ?Caveposition
    {
        return $this->caveposition;
    }
}