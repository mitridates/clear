<?php
namespace  App\Entity\Cave\Trait;
use App\Entity\Cave\Cave;

/**
 * Trait to include in ManyToOne Entities
 */
trait CaveManyToOneTrait
{
    public function getCave(): Cave
    {
        return $this->cave;
    }
    public function setCave(Cave $cave): self
    {
        $this->cave = $cave;
        return $this;
    }
}