<?php

namespace App\Area\UI\Form;

use App\Geonames\UI\Form\EventSubscriber\Admin1FieldSubscriber;
use App\Geonames\UI\Form\EventSubscriber\CountryFieldSubscriber;
use App\Shared\UI\Form\FormFields\AbstractFormFields;
use App\Shared\UI\Form\FormFields\AdministrativeDivisionSubscriberTrait;

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
        [ 'country', CountryFieldSubscriber::class, ['options'=>[
            'label'=>'',
            'required' => true,
            'attr'=>['field_id'=>224]
        ]]],
        ['admin1',Admin1FieldSubscriber::class, ['options'=>[
            'label'=>'',
            'required' => true,
            'attr'=>['field_id'=>225]
        ]]]
    ];
}

}