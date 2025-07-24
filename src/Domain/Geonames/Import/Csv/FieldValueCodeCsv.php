<?php
namespace App\Domain\Geonames\Import\Csv;

use App\Entity\FieldDefinition\Fieldvaluecode;
use Doctrine\ORM\EntityRepository;

class FieldValueCodeCsv extends AbstractCsv
{

    const  COLS= ['id', 'field','code','value'];

    public function __construct(private readonly EntityRepository $repository)
    {
    }


    /**
     * Create/update Fielddefinition from csv array
     */
    public function getEntity(array $data): Fieldvaluecode
    {
        $id = $data[array_search('id', self::COLS)];
        $registry = $this->repository->find($id);
        if(!$registry){
            $entity = new Fieldvaluecode();
            $reflection = new \ReflectionClass($entity);
            $property = $reflection->getProperty('id');
            $property->setAccessible(true);
            $property->setValue($entity, $id);
            $registry = $entity;
        }
        foreach (self::COLS as $col) {
            if($col==='id') continue;
            $val = $data[array_search($col, self::COLS)];
            $isNull= strcasecmp($val, 'null')===0;
            $registry->{'set'.ucfirst($col)}($isNull ? null : $val);
        }
        return $registry;
    }

}