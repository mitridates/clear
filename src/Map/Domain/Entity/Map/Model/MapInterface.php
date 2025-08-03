<?php

namespace App\Map\Domain\Entity\Map\Model;

use App\Map\Domain\Entity\Map\Map;

interface MapInterface
{
    public function getMap(): Map;
}