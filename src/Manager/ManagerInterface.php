<?php

namespace App\Manager;

use App\Entity\Area;

interface ManagerInterface
{

    public function persist(Area $area);
}