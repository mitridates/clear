<?php
namespace  App\Cave\Domain\Entity\Trait;
use App\Cave\Domain\Entity\Cave;

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