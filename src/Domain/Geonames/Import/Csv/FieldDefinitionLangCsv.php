<?php
namespace App\Domain\Geonames\Import\Csv;

use App\Domain\Fielddefinition\Entity\Fielddefinitionlang;
use Doctrine\ORM\EntityRepository;

class FieldDefinitionLangCsv extends AbstractCsv
{

    const  COLS= ["locale","id","review","name","abbreviation","definition","example","comment","uso"];

    public function __construct(private readonly EntityRepository $fdlRepository, private readonly EntityRepository $fdRepository)
    {
    }

    /**
     * Create/update Fielddefinition from csv array
     */
    public function getEntity(array $data): Fielddefinitionlang
    {
        $id = $data[array_search('id', self::COLS)];
        $locale = $data[array_search('locale', self::COLS)];
        $registry = $this->fdlRepository->findOneBy(['id'=>$id, 'locale'=>$locale]) ?? (new Fielddefinitionlang( $this->fdRepository->find($id)))->setLocale($locale);

        foreach (self::COLS as $col) {
                if(in_array($col, ['id', 'locale'])) continue;
                $val = $data[array_search($col, self::COLS)];
                $isNull= strtolower($val)==='null' || $val==='';
                $registry->{'set'.ucfirst($col)}($isNull ? null : $val);
            }
        return $registry;
    }

}