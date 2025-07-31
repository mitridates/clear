<?php
namespace  App\Domain\Map\Entity\Map\Trait;
use App\Domain\Map\Entity\Map\Map;

/**
 * Trait to include in OneToOne Entities
 */
trait MapManyToOneTrait
{
    public function __construct(Map $map)
    {
        $this->map = $map;
    }

    public function getMap(): Map
    {
        return $this->map;
    }
}