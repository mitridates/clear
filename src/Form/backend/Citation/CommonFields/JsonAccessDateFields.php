<?php

namespace App\Form\backend\Citation\CommonFields;

use App\Entity\Citation\Citation;
use App\Form\FormFields\JsonFormFieldsEventInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormInterface;

class JsonAccessDateFields extends JsonDateFields implements JsonFormFieldsEventInterface
{
    public function __construct(FormBuilderInterface $builder)
    {
        parent::__construct($builder);
        $form= $builder->getForm();
        /** @var Citation $citation */
        $citation = $form->getData();
        $json= json_decode($citation->getJsondata(), true);

        $this->
        renameField('year', 'ayear')->
        setFieldOptions('ayear',  [
            'label'=>'Access year',
        ])->
        renameField('month', 'amonth')->
        setFieldOptions('amonth',  [
            'label'=>'Access month',
            'choices'=>self::getMonthChoices(),
        ])->
        renameField('day', 'aday')->
        setFieldOptions('aday',  [
            'label'=>'Access day',
            'choices'=>self::getDaysInMonthYear($json['amonth']??null,$json['ayear']??null)
        ])
            ;
        foreach(['ayear', 'amonth', 'aday'] as $v){
            if(isset($json[$v])){
                $this->setFieldOptions($v, ['data'=> $json[$v]]);
            }
        }
    }

    public static function setPostSubmitJsonData(FormInterface $form, array &$data): void
    {
        foreach (['ayear', 'amonth', 'aday'] as $d){
            $v= $form->get($d)->getData();
            if($v){
               $data[$d]=$v; 
            }else{
                unset($data[$d]);
            } 
        }
    }

    /**
     *
     * The choices[] option MUST contain the POST value selected if exists or will send a FormError.
     * @param FormEvent $event
     * @return void
     */
    public static function setPreSubmitData(FormEvent $event): void
    {
        $data= $event->getData();
        $form= $event->getForm();
        $add=  ['aday', ChoiceType::class, [
            'label'=>'Day',
            'required'=>false,
            'choices' => isset($data['aday'])?[$data['aday'] => $data['aday']]: null,
            'mapped'=>false,
            'data'=>$data['aday']??null,
            'placeholder'=>'No day',
            'attr'=>['class'=>'js-day'],
        ]];
        call_user_func_array([$form, 'add'], $add);
    }
}