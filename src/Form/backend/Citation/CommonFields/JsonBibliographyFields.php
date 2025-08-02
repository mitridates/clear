<?php

namespace App\Form\backend\Citation\CommonFields;

use App\Domain\Citation\Entity\Citation;
use App\Form\FormFields\AbstractFormFields;
use App\Form\FormFields\JsonFormFieldsEventInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;

class JsonBibliographyFields extends AbstractFormFields implements JsonFormFieldsEventInterface
{
    public function __construct(FormBuilderInterface $builder)
    {
        $form= $builder->getForm();
        /** @var Citation $citation */
        $citation = $form->getData();
        $json= json_decode($citation->getJsondata(), true);
        $this->fields= [
            ['publisher', null, [
                'label'=>'Publisher and city',
                'required'=>false,
                'mapped'=>false,
                'data'=> $json['publisher']??null
                ]
            ],
            ['issue', null, [
                'label'=>'Issue number',
                'help'=>'Texto de ayuda',
                'required'=>false,
                'mapped'=>false,
                'data'=> $json['issue']??null,
                'attr'=>['style'=>'width: 8em']
            ]],
            ['isbn', null, [
                'label'=>'ISBN',
                'required'=>false,
                'mapped'=>false,
                'data'=> $json['isbn']??null
            ]
            ],
            ['issn', null, [
                'label'=>'ISSN',
                'required'=>false,
                'mapped'=>false,
                'data'=> $json['issn']??null
            ]
            ],
            ['copyright', null, [
                'label'=>'Copyright',
                'required'=>false,
                'mapped'=>false,
                'data'=> $json['copyright']??null
            ]
            ],
            ['ldn', null, [
                'label'=>'Legal Deposit Number',
                'required'=>false,
                'mapped'=>false,
                'data'=> $json['ldn']??null
            ]
            ],
            ['edition', null, [
                'label'=>'Edition',
                'required'=>false,
                'mapped'=>false,
                'data'=> $json['edition']??null
            ]
            ],
            ['medium', null, [
                'label'=>'Medium',
                'required'=>false,
                'mapped'=>false,
                'data'=> $json['medium']??null
            ]
            ]
        ];

    }

    /**
     * @inheritDoc
     */
    public static function setPostSubmitJsonData(FormInterface $form, array &$data): void
    {
        foreach (['publisher', 'issue', 'isbn', 'issn', 'mapsnumber', 'platesnumber','copyright', 'ldn', 'edition', 'medium'] as $d)
        {
            if(!$form->has($d)) continue;

            $v= $form->get($d)->getData();
            if($v || $v!==''){
                  $data[$d]=$v;
            }else{
                 unset($data[$d]);
            }
        }
    }

}