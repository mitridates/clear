<?php
namespace App\Domain\Geonames\Import\Csv;

abstract class AbstractCsv
{

    /**
     * Create/update Entity from csv array
     */
    abstract public function getEntity(array $data): object;
}