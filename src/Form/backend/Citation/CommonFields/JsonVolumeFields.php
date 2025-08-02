<?php

namespace App\Form\backend\Citation\CommonFields;

use App\Domain\Citation\Entity\Citation;
use App\Form\FormFields\AbstractFormFields;
use App\Form\FormFields\JsonFormFieldsEventInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;

class JsonVolumeFields extends AbstractFormFields implements JsonFormFieldsEventInterface
{

    public function __construct(FormBuilderInterface $builder)
    {
        $form= $builder->getForm();
        /** @var Citation $citation */
        $citation = $form->getData();
        $json= json_decode($citation->getJsondata(), true);
        $this->fields= [
            ['volume', null, [
                'label'=>'Volume',
                'required'=>false,
                'mapped'=>false,
                'data'=> $json['volume']??null,
                'attr'=>['class'=>'js-volume']

            ]],
            ['volume_end', null, [
                'label'=>'Last volume',
                'required'=>false,
                'mapped'=>false,
                'data'=> $json['volume_end']??null,
                'attr'=>['class'=>'js-volume-end']

            ]],
            ['volume_range', CheckboxType::class, [
                'label'=>'Volume range',
                'required'=>false,
                'mapped'=>false,
                'attr'=>['class'=>'js-volume-range']

            ]]
        ];
    }
    /**
     * @inheritDoc
     */   
    public static function setPostSubmitJsonData(FormInterface $form, array &$data): void
    {
        $volume= $form->get('volume')->getData();
        $end= $form->get('volume_end')->getData();
        
        if($volume){
            $data['volume']=$volume;
            if($end){
                $data['volume_end']=$end;
            }else{
                unset($data['volume_end']);
            }
        }else{
            unset($data['volume'], $data['volume_end']);
        }
    }
}