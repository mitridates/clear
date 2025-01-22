<?php
namespace App\Utils\Import\Csv;

use App\Entity\FieldDefinition\Fieldvaluecodelang;
use Doctrine\ORM\EntityRepository;

class FieldValueCodeLangCsv extends AbstractCsv
{

    const  COLS= ['locale','id','value'];

    public function __construct(private readonly EntityRepository $FVCLRepository, private readonly EntityRepository $FVCRepository)
    {
    }


    /**
     * Create/update Fielddefinition from csv array
     */
    public function getEntity(array $data): Fieldvaluecodelang
    {
        $id = $data[array_search('id', self::COLS)];
        $locale = $data[array_search('locale', self::COLS)];
        $registry = $this->FVCLRepository->findOneBy(['id'=>$id, 'locale'=>$locale]) ?? (new Fieldvaluecodelang( $this->FVCRepository->find($id)))->setLocale($locale);

        foreach (self::COLS as $col) {
                if(in_array($col, ['id', 'locale'])) continue;
                $val = $data[array_search($col, self::COLS)];
                $isNull= strtolower($val)==='null' || $val==='';
                $registry->{'set'.ucfirst($col)}($isNull ? null : $val);
            }
        return $registry;
    }

}