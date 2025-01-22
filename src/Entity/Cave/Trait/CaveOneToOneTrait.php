<?php
namespace App\Entity\Cave\Trait;
use App\Entity\Cave\Trait\Cave;

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