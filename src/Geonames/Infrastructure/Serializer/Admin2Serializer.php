<?php

namespace App\Geonames\Infrastructure\Serializer;
use App\Geonames\Domain\Entity\Admin2;
use App\Shared\reflection\EntityReflectionHelper;
use App\Shared\tobscure\jsonapi\AbstractSerializer;

class Admin2Serializer extends AbstractSerializer
{

    protected $type = 'admin2';

    /**
     * @param Admin2 $model
     * @param ?array $fields
     * @return array
     */
    public function getAttributes($model, array $fields = null): array
    {
        $data= EntityReflectionHelper::serializeClassProperties($model, $fields);
        if($fields && in_array('country', $fields) && $data['country']){
            $data['country']= $model->getCountry()->getName();
        }
        if($fields && in_array('admin1', $fields) && $data['admin1']){
            $data['admin1']= $model->getAdmin1()->getName();
        }
        return $data;

//        if(is_array($fields)){
//            $a= [];
//                foreach ($fields as $f){
//                    $fn='get'.ucfirst(strtolower($f));
//                    $a[$f] = $model->$fn();
//                }
//                return $a;
//            }
//
//        return [
//            'id'=>$model->getId(),
//            'name'=>$model->getName()
//        ];
    }
//
//    /**
//     * @param Admin2 $model
//     * @return string
//     */
//    public function getId($model)
//    {
//        return $model->getId();
//    }
}