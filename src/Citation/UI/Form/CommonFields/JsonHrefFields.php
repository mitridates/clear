<?php

namespace App\Citation\UI\Form\CommonFields;

use App\Citation\Domain\Entity\Citation;
use App\Form\FormFields\AbstractFormFields;
use App\Form\FormFields\JsonFormFieldsEventInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Url;

class JsonHrefFields extends AbstractFormFields implements JsonFormFieldsEventInterface
{


    public function __construct(FormBuilderInterface $builder)
    {
        $form= $builder->getForm();
        /** @var Citation $citation */
        $citation = $form->getData();
        $json= json_decode($citation->getJsondata(), true);

        $this->fields= [
            ['url', null,
                [
                    'constraints' => [
                        new Url(),
                        new Length(['min' => 10, 'max'=>512]),
                    ],
                    'label'=>'Url',
                    'required'=>false,
                    'mapped'=>false,
                    'data'=> $json['url']??null,
                ]],
            ['pdf', null, [
                'constraints' => [
                    new Url(),
                    new Length(['min' => 10, 'max'=>512]),
                ],
                'label'=>'Pdf',
                'required'=>false,
                'mapped'=>false,
                'data'=> $json['pdf']??null,
            ]]
        ];
    }

    public static function setPostSubmitJsonData(FormInterface $form, array &$data): void
    {
        foreach (['url', 'pdf'] as $d){
            if(!$form->has($d)){
                continue;
            }
            $v= $form->get($d)->getData();
            if($v) {
                $data[$d]=$v;
            }else {
                unset($data[$d]);
            }
        }
    }
}