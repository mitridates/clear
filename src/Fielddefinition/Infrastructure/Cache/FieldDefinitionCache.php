<?php

namespace App\Fielddefinition\Infrastructure\Cache;
use App\Fielddefinition\Domain\Entity\Fielddefinition;
use App\Fielddefinition\Domain\Entity\Fielddefinitionlang;
use App\Fielddefinition\Domain\Manager\FieldDefinitionManager;
use App\Fielddefinition\Infrastructure\Serializer\FieldDefinitionLangSerializer;
use App\Fielddefinition\Infrastructure\Serializer\FieldDefinitionSerializer;
use App\Shared\Infrastructure\Cache\FilesCache;
use App\Shared\tobscure\jsonapi\Document;
use App\Shared\tobscure\jsonapi\Resource;
use DateTime;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Cache\ItemInterface;

class FieldDefinitionCache extends FilesCache
{
    public FieldValueCodeCache $valueCodeCache;
    public function __construct(#[Autowire('%kernel.project_dir%')] string $projectDir, #[Autowire('%kernel.environment%')] string $env)
    {
        $this->valueCodeCache= new FieldValueCodeCache($projectDir, $env);
        parent::__construct($projectDir, $env);
    }

    const PREFIX= 'fieldefinition.';
    const SUFFIX= 'id.';
    const SUFFIX_TRANS= 'trans.';
    public function updateFieldDefinition(int $id, array $fd):array
    {
        $this->cache->clear(self::PREFIX.self::SUFFIX.$id);
        return $this->cache->get(self::PREFIX.self::SUFFIX.$id, function(ItemInterface $item) use ($fd){
            $item->tag('fieldefinition');
            return $fd;
        });
    }

    /**
     * @throws InvalidArgumentException
     */
    public function getFieldDefinition(int $id, ?string $locale=null): array|false
    {
        $item = $this->cache->getItem(self::PREFIX.self::SUFFIX.$id);
        if(!$item->isHit()) return false;
        $fdCache=$item->get();
        $meta = &$fdCache['data']['meta'];
        $attr= &$fdCache['data']['attributes'];

        $date_cache= \DateTimeImmutable::createFromInterface(new DateTime($meta['_cached']));
        $date_now= new \DateTimeImmutable();


        $meta= array_merge_recursive($meta, [
            '_locale'=>$locale,
            '_randid'=> 'fd.'.$fdCache['data']['id'].'.'.rand(),
            '_cache_diff'=> $date_cache->diff($date_now)->format('%a Days  %Hh:%im:%ss'),

           // '_locales'=>array_keys($meta['translations']),
        ]);

        if($locale){
            if(isset($meta['translations'][$locale]))
            {
                $meta['source']=$attr;
                foreach ($meta['translations'][$locale] as $k => $v){
                    $attr[$k]=$v;
                }
            }
        }
        unset($meta['translations']);


        $meta['fieldValueCodes']=($attr['valuecode'])? $this->valueCodeCache->get($attr['valuecode']):[];
        foreach ($meta['fieldValueCodes'] as $k => &$v)
        {
            $loc = $v['locale'][$locale] ?? null;
            if(isset($loc)){
                $v['value']= $loc['value'];
                unset($v['locale']);
            }
            unset($v['locale']);
        }

        return $fdCache;
    }

    public function getFieldDefinitions(array $ids)
    {
        $ret = [];
        foreach ($ids as $id){
            $ret[]=$this->getFieldDefinition($id);
        }
        return $ret;
    }

    /**
     * @throws InvalidArgumentException
     * @throws \Exception
     */
    public function warmup(FieldDefinitionManager $manager, Fielddefinition|int $fd, ?UrlGeneratorInterface $urlGenerator= null): FieldDefinitionCache
    {
        if(is_int($fd)) $fd = $manager->repo->find($fd);

        //if(!$fd) return $this;//not found

        $fdResource = (new Resource($fd, new FieldDefinitionSerializer($urlGenerator)));

        //add Field definition Translation
        $trans=[];
        foreach($fd->getTranslations()->getIterator() as $i => /**@var Fielddefinitionlang $item */$item)
        {
            $langResource =  (new Resource($item, new FieldDefinitionLangSerializer($urlGenerator)));
            $trans[$item->getLocale()]= $langResource->getAttributes();
        }
        $fdResource->addMeta('translations', $trans);


        if($fd->getValuecode()){
            $fieldValueCodes= $this->valueCodeCache->get($fd->getValuecode());
            if(!$fieldValueCodes) $this->valueCodeCache->warmup($manager, $fd->getValuecode());
        }

        //add value codes
//        $fieldValueCodes = ($fd->getValuecode())? $manager->getValueCodesByField($fd->getValuecode()) : [];
//        $codes=[];
//
//        if($fd->getValuecode())
//        {
//            foreach ($fieldValueCodes as $i=> /**@var Fieldvaluecode $fvc */ $fvc)
//            {
//                $codeResource =  new Resource($fvc, new FieldValueCodeSerializer());
//                $codes[$i]=$codeResource->getAttributes();
//                $codes[$i]['locale']=[];
//                foreach($fvc->getTranslations()->getIterator() as $ii => /**@var Fieldvaluecodelang $fvcl */$fvcl)
//                {
//                    $fvclTransResource =  new Resource($fvcl, new FieldValueCodeLangSerializer());
//                    $codes[$i]['locale'][$fvcl->getLocale()]= $fvclTransResource->getAttributes();
//                }
//            }
//            $this->valueCodeCache->updateFieldValueCode($fd->getValuecode(), $codes);
//
//        }

//        $fdResource->addMeta('fieldValueCodes', $codes);



        $fdResource->addMeta('_cached', (new \DateTimeImmutable())->format(\DateTimeInterface::RFC850));

        $fdDoc= new Document($fdResource);
//        $data= $fdDoc->toArray();
//        $data['data']['attributes']['property']
        $this->updateFieldDefinition($fd->getId(), $fdDoc->toArray());
        return $this;
    }

    public function warmupAll(FieldDefinitionManager $manager, UrlGeneratorInterface $urlGenerator): FieldDefinitionCache
    {
        foreach ($manager->repo->findAll() as $fd){
            $this->warmup($manager, $fd, $urlGenerator);
        }
        return $this;
    }


}