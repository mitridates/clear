<?php

namespace App\Map\Domain\Entity\Map\Model;

use App\Map\Domain\Entity\Map\Map;

interface MapManyToOneInterface extends MapInterface
{
    public function getMap(): Map;
    public function getSequence(): ?int;
    public function setSequence(int $sequence): self;
    public function getPosition(): ?int;
    public function setPosition(?int $position): self;
}