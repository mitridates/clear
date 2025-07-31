<?php

namespace App\Domain\Map\Entity\Map\Model;

use App\Domain\Map\Entity\Map\Map;

interface MapInterface
{
    public function getMap(): Map;
}