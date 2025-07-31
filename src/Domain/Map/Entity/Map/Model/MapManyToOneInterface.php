<?php

namespace App\Domain\Map\Entity\Map\Model;

use App\Domain\Map\Entity\Map\Map;

interface MapManyToOneInterface extends MapInterface
{
    public function getMap(): Map;
    public function getSequence(): ?int;
    public function setSequence(int $sequence): self;
    public function getPosition(): ?int;
    public function setPosition(?int $position): self;
}