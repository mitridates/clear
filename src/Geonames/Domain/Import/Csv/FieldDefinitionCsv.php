<?php
namespace App\Geonames\Domain\Import\Csv;

use App\Fielddefinition\Domain\Entity\Fielddefinition;
use Doctrine\ORM\EntityRepository;

class FieldDefinitionCsv extends AbstractCsv
{

    const  COLS= ["id","entity","datatype","maxlength","coding","singlemultivalued","valuecode","name","abbreviation","definition","example","comment","uso"];

    public function __construct(private readonly EntityRepository $repository)
    {
    }


    /**
     * Create/update Fielddefinition from csv array
     */
    public function getEntity(array $data): Fielddefinition
    {
        $id = $data[array_search('id', self::COLS)];
        $registry = $this->repository->find($id);
        if(!$registry){
            $entity = new Fielddefinition();
            $reflection = new \ReflectionClass($entity);
            $property = $reflection->getProperty('id');
            $property->setAccessible(true);
            $property->setValue($entity, $id);
            $registry = $entity;
        }
        foreach (self::COLS as $col)
        {
            if($col==='id') continue;
            $val = $data[array_search($col, self::COLS)];

            if(is_null($val) || (is_string($val) && strtolower($val)==='null')){
                $val= null;
            }elseif (in_array($col, ['field', 'valuecode'])){
                $val= (int)$val;
            }

            $registry->{'set'.ucfirst($col)}($val);
        }
        return $registry;
    }

}