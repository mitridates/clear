<?php

namespace App\Services\Cache\FilesCache;

use App\Domain\JsonApi\Serializers\FieldValueCodeLangSerializer;
use App\Domain\JsonApi\Serializers\FieldValueCodeSerializer;
use App\Entity\FieldDefinition\Fieldvaluecode;
use App\Entity\FieldDefinition\Fieldvaluecodelang;
use App\Manager\FieldDefinitionManager;
use App\Services\Cache\FilesCache;
use App\Shared\tobscure\jsonapi\Resource;
use Psr\Cache\InvalidArgumentException;
use Symfony\Contracts\Cache\ItemInterface;

class FieldValueCodeCache extends FilesCache
{
    const PREFIX= 'valuecodes.';
    const SUFFIX= 'field.';

    public function update(string $field, array $valueCodes):array
    {
        $this->cache->clear(self::PREFIX.self::SUFFIX.$field);
        return $this->cache->get(self::PREFIX.self::SUFFIX.$field, function(ItemInterface $item) use ($valueCodes){
            $item->tag(self::PREFIX);
            return $valueCodes;
        });
    }

    /**
     * @param string $field
     * @return false|array
     * @throws InvalidArgumentException
     */
    public function get(string $field): array|false
    {
            $item = $this->cache->getItem(self::PREFIX.self::SUFFIX.$field);
            return ($item->isHit())? $item->get() : false;
    }

    /**
     * @throws \Exception
     */
    public function warmup(FieldDefinitionManager $manager, $field): FieldValueCodeCache
    {
        $fieldValueCodes = $manager->getValueCodesByField($field);
        $codes=[];
        foreach ($fieldValueCodes as $i=> /**@var Fieldvaluecode $fvc */ $fvc)
        {
            $codeResource =  new Resource($fvc, new FieldValueCodeSerializer());
            $codes[$i]=$codeResource->getAttributes();
            $codes[$i]['locale']=[];
            foreach($fvc->getTranslations()->getIterator() as $ii => /**@var Fieldvaluecodelang $fvcl */$fvcl)
            {
                $fvclTransResource =  new Resource($fvcl, new FieldValueCodeLangSerializer());
                $codes[$i]['locale'][$fvcl->getLocale()]= $fvclTransResource->getAttributes();
            }
        }
        $this->update($field, $codes);

        return $this;
    }

}