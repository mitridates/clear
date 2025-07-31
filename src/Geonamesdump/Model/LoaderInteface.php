<?php
namespace App\Geonamesdump\Model;
use App\Domain\Geonames\Entity\Admin1;
use App\Domain\Geonames\Entity\Admin2;
use App\Domain\Geonames\Entity\Admin3;
use App\Domain\Geonames\Entity\Country;
use App\Domain\Geonames\Entity\Geonames;

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