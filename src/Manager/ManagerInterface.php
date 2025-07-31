<?php

namespace App\Manager;

use App\Domain\Area\Entity\Area;

interface ManagerInterface
{

    public function persist(Area $area);
}