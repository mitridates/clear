<?php

namespace App\Form\backend\Specie;

use App\Form\FormFields\AbstractFormFields;

class SpecieFields extends AbstractFormFields
{
    public function __construct()
    {
        $this->fields=[
            //textType
            ['genus', null, ['label'=>'','attr'=>['field_id'=>281]]],
            ['phylum', null, ['label'=>'','attr'=>['field_id'=>605]]],
            ['class', null, ['label'=>'','attr'=>['field_id'=>604]]],
            ['orden', null, ['label'=>'','attr'=>['field_id'=>603]]],
            ['family', null, ['label'=>'','attr'=>['field_id'=>602]]],
            ['name', null,
                [
                    'required'=>true,
                    'label'=>'',
                    'attr'=>['field_id'=>282]
                ]
            ],
            ['commonname', null,
                [
                    'required'=>true,
                    'label'=>'',
                    'attr'=>['field_id'=>606]]
            ],

        ];
    }

}