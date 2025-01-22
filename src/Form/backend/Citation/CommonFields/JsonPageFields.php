<?php

namespace App\Form\backend\Citation\CommonFields;

use App\Entity\Citation\Citation;
use App\Form\FormFields\AbstractFormFields;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Constraints\Length;

class JsonPageFields extends AbstractFormFields
{

    public function __construct(FormBuilderInterface $builder)
    {
        $form= $builder->getForm();
        /** @var Citation $citation */
        $citation = $form->getData();
        $json= json_decode($citation->getJsondata(), true);

        $this->fields= [
            ['page', null, [
                'constraints' => [
                    new Length(['max'=>120]),
                ],
                'label'=>'Page',
                'required'=>false,
                'mapped'=>false,
                'data'=> $json['page']??null,
                'attr'=>[
                    'class'=>'js-page',
//                    'style'=>'width: 8em'
                ]
            ]],
            ['page_end', null, [
                'constraints' => [
                    new Length(['max'=>120]),
                ],
                'label'=>'Last page',
                'required'=>false,
                'mapped'=>false,
                'data'=> $json['page_end']??null,
                'attr'=>[
                    'class'=>'js-page-end',
//                    'style'=>'width: 8em'
                ]
            ]],
            ['page_range', CheckboxType::class, [
                'label'=>'Page range',
                'required'=>false,
                'mapped'=>false,
                'attr'=>['class'=>'js-page-range']
            ]]

        ];
    }

    /** @inheritDoc */
    public static function setPostSubmitJsonData(FormInterface $form, array &$data): void
    {
        $page= $form->get('page')->getData();
        $end=  $form->get('page_end')->getData();
        if($page){
            $data['page']=$page;
            if($end){
                $data['page_end']=$end;
            }else{
                unset($data['page_end']);
            }
        }else{
            unset($data['page'], $data['page_end']);
        }
    }
}