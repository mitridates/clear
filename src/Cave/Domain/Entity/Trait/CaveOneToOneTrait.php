<?php
namespace App\Cave\Domain\Entity\Trait;
use App\Cave\Domain\Entity\Cave;

/**
 * Trait to include in OneToOne Entities
 */
trait CaveOneToOneTrait
{

    public function __construct(Cave $cave)
    {
        $this->cave = $cave;
    }

    public function getCave(): Cave
    {
        return $this->cave;
    }
}