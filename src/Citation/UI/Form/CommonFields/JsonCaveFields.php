<?php

namespace App\Citation\UI\Form\CommonFields;

use App\Citation\Domain\Entity\Citation;
use App\Shared\UI\Form\FormFields\AbstractFormFields;
use App\Shared\UI\Form\FormFields\JsonFormFieldsEventInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;

class JsonCaveFields extends AbstractFormFields implements JsonFormFieldsEventInterface
{
    public function __construct(FormBuilderInterface $builder)
    {
        $form= $builder->getForm();
        /** @var Citation $citation */
        $citation = $form->getData();
        $json= json_decode($citation->getJsondata(), true);
        $this->fields= [

            ['mapsnumber', null, [
                'label'=>'Quantity of maps',
                'required'=>false,
                'mapped'=>false,
                'data'=> $json['mapsnumber']??null
            ]
            ],
            ['platesnumber', null, [
                'label'=>'Quantity of plates',
                'required'=>false,
                'mapped'=>false,
                'data'=> $json['platesnumber']??null
            ]
            ]

        ];

    }

    /**
     * @inheritDoc
     */
    public static function setPostSubmitJsonData(FormInterface $form, array &$data): void
    {
        foreach (['mapsnumber', 'platesnumber'] as $d)
        {
            $v= $form->get($d)->getData();
            if($v){
                  $data[$d]=$v;
            }else{
                 unset($data[$d]);
            }
        }
    }

}