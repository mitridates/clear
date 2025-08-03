<?php
namespace App\Geonames\Domain\Dump\Model;
use App\Geonames\Domain\Entity\Admin1;
use App\Geonames\Domain\Entity\Admin2;
use App\Geonames\Domain\Entity\Admin3;
use App\Geonames\Domain\Entity\Country;
use App\Geonames\Domain\Entity\Geonames;

interface LoaderInteface
{
    /**
     * Relation array(property names) <-> explode(line)
     * @return array
     */
    public function getCsvOrderedCols(): array;

    /**
     * Set Entity from cvs line
     * @return Geonames|Country|Admin1|Admin2|Admin3|null
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function setEntity(string|\SimpleXMLElement $data);

    /**
     * Read and load.
     * @return LoaderInteface
     * @throws \Exception
     */
    public function load(): LoaderInteface;
}