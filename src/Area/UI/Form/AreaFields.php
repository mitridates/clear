<?php

namespace App\Area\UI\Form;

use App\Form\EventListener\AddAdmin1FieldSubscriber;
use App\Form\EventListener\AddCountryFieldSubscriber;
use App\Form\FormFields\AbstractFormFields;
use App\Form\FormFields\AdministrativeDivisionSubscriberTrait;

class AreaFields extends AbstractFormFields
{
 use AdministrativeDivisionSubscriberTrait;

public function __construct()
{
    $this->fields= [
        //textType
        ['name', null, ['label'=>'Area name','required' => true,'attr'=>['field_id'=>80]]],
        ['code', null, ['label'=>'','required' => true,'attr'=>['field_id'=>81]]],
        ['comment', null, ['label'=>'','attr'=>['field_id'=>621]]],
        ['mapsheet', null, ['label'=>'','attr'=>['field_id'=>618]]]
    ];

    $this->subscribers= [
        [ 'country', AddCountryFieldSubscriber::class, ['options'=>[
            'label'=>'',
            'required' => true,
            'attr'=>['field_id'=>224]
        ]]],
        ['admin1',AddAdmin1FieldSubscriber::class, ['options'=>[
            'label'=>'',
            'required' => true,
            'attr'=>['field_id'=>225]
        ]]]
    ];
}

}