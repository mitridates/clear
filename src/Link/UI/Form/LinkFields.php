<?php

namespace App\Link\UI\Form;

use App\Map\UI\Form\EventSubscriber\MimeTypeSubscriber;
use App\Organisation\UI\Form\EventSubscriber\OrganisationFieldSubscriber;
use App\Person\UI\Form\EventSubscriber\PersonFieldSubscriber;
use App\Shared\UI\Form\FormFields\AbstractFormFields;
use App\Shared\UI\Form\FormFields\AdministrativeDivisionSubscriberTrait;
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
        [ 'organisation', OrganisationFieldSubscriber::class, ['options'=>[
            'required' => false,
            'attr'=>['field_id'=>1001]
        ]]],
        ['author',PersonFieldSubscriber::class, [
            'name'=>'author',
            'getMethod'=>'getAuthor',
            'options'=>[
            'required' => false,
            'attr'=>['field_id'=>1003]
        ]]],
        ['mime', MimeTypeSubscriber::class, [
            'options'=>['required' => false,'attr'=>['field_id'=>1007]]]
        ]
    ];
}

}