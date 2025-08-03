<?php
namespace App\Geonames\Domain\Import\Csv;

abstract class AbstractCsv
{

    /**
     * Create/update Entity from csv array
     */
    abstract public function getEntity(array $data): object;
}