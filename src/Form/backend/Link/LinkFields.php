<?php

namespace App\Form\backend\Link;

use App\Form\EventListener\AddMimeTypeSubscriber;
use App\Form\EventListener\AddOrganisationFieldSubscriber;
use App\Form\EventListener\AddPersonFieldSubscriber;
use App\Form\FormFields\AbstractFormFields;
use App\Form\FormFields\AdministrativeDivisionSubscriberTrait;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;

class LinkFields extends AbstractFormFields
{
 use AdministrativeDivisionSubscriberTrait;

public function __construct()
{
    $this->fields= [
        //textType
        ['title', null, ['attr'=>['field_id'=>1000]]],
        ['organisationname', null, ['attr'=>['field_id'=>1002]]],
        ['authorname', null, ['attr'=>['field_id'=>1004]]],
        ['url', UrlType::class, [
            'required'=>true,'attr'=>['field_id'=>1006]
        ]],
        ['accessed', DateType::class, [
            // renders it as a single text box
            'widget' => 'single_text',
            'required'=>false,
            'attr'=>['field_id'=>1005]
        ]]

    ];

    $this->subscribers= [
        [ 'organisation', AddOrganisationFieldSubscriber::class, ['options'=>[
            'required' => false,
            'attr'=>['field_id'=>1001]
        ]]],
        ['author',AddPersonFieldSubscriber::class, [
            'name'=>'author',
            'getMethod'=>'getAuthor',
            'options'=>[
            'required' => false,
            'attr'=>['field_id'=>1003]
        ]]],
        ['mime', AddMimeTypeSubscriber::class, [
            'options'=>['required' => false,'attr'=>['field_id'=>1007]]]
        ]
    ];
}

}