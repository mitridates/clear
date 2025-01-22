<?php
namespace App\Utils\Import\Csv;

abstract class AbstractCsv
{

    /**
     * Create/update Entity from csv array
     */
    abstract public function getEntity(array $data): object;
}