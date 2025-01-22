<?php

namespace App\Utils\Json\Serializers\Geonames;
use App\Entity\Geonames\Admin3;
use App\Utils\reflection\EntityReflectionHelper;
use App\vendor\tobscure\jsonapi\AbstractSerializer;

class Admin3Serializer extends AbstractSerializer
{

    protected $type = 'admin3';

    /**
     * @param Admin3 $model
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
        if($fields && in_array('admin2', $fields) && $data['admin2']){
            $data['admin2']= $model->getAdmin2()->getName();
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
//     * @param Admin3 $model
//     * @return string
//     */
//    public function getId($model)
//    {
//        return $model->getId();
//    }
}