<?php
namespace App\Domain\Geonames\Import\Csv;

use App\Domain\Fielddefinition\Entity\Fieldvaluecode;
use App\Domain\Geonames\Entity\Admin1;
use App\Domain\Geonames\Entity\Admin2;
use App\Domain\Geonames\Entity\Admin3;
use App\Domain\Geonames\Entity\Country;
use App\Entity\Organisation;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class OrganisationCsv extends AbstractCsv
{

    const  COLS= [
        "id","type","currentidifdefunct","country","admin1","admin2","admin3","countryaddress","admin1address","admin2address","admin3address",
        "coverage","grouping","isgenerator","code","postcode","postcodefist","defunct",
        "defunctyear","addressline0","addressline0","addressline2","addressline3","addressline4",
        "initials","name","email","webpage","created","updated","hidden"
    ];

    public function __construct(private readonly EntityRepository $repository, private readonly EntityManagerInterface $em)
    {
    }


    /**
     * Create/update Fielddefinition from csv array
     */
    public function getEntity(array $data): Organisation
    {
        $id = $data[array_search('id', self::COLS)];

        $registry = $this->repository->find($id);
        if(!$registry){
            $entity = new Organisation();
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
            if($col==='code'){
                var_dump($val);
                var_dump(is_null($val));
            }

            if(is_null($val) || (is_string($val) && 'null'===strtolower($val))){
                $val= null;
            }elseif (in_array($col, ['type', 'coverage', 'grouping'])){
//                $val= $this->setEntityReflectionId($val, new Fieldvaluecode());
                $val= $this->em->getRepository(Fieldvaluecode::class)->find($val);
            }elseif ($col==='currentidifdefunct'){
                $val= $this->em->getRepository(Organisation::class)->find($val);
//                $val= $this->setEntityReflectionId($val, new Organisation());
            }elseif ($col==='country' || $col==='countryaddress'){
                $val= $this->em->getRepository(Country::class)->find($val);
//                $val= $this->setEntityReflectionId($val, new Country());
            }elseif ($col==='admin1' || $col==='admin1address'){
//                $val= $this->setEntityReflectionId($val, new Admin1());
                $val= $this->em->getRepository(Admin1::class)->find($val);
            }elseif ($col==='admin2' || $col==='admin2address'){
                $val= $this->em->getRepository(Admin2::class)->find($val);
//                $val= $this->setEntityReflectionId($val, new Admin2());
            }elseif ($col==='admin3' || $col==='admin3address'){
                $val= $this->em->getRepository(Admin3::class)->find($val);
//                $val= $this->setEntityReflectionId($val, new Admin3());
            }elseif (in_array($col, ['defunctyear'])){
                $val= (int)$val;
            }elseif (in_array($col, ['created', 'updated'])){
                $format= 'Y-m-d H:i:s';
                $val= DateTime::createFromFormat($format, $val);
            }


            $registry->{'set'.ucfirst($col)}($val);
        }
        return $registry;
    }

    private function setEntityReflectionId(int|string $val, object $entity): object
    {
        $org = new Organisation();
        $reflection = new \ReflectionClass($entity);
        $property = $reflection->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($entity, $val);
        return $entity;
    }

}