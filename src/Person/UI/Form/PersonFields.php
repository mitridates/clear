<?php

namespace App\Person\UI\Form;

use App\Geonames\UI\Form\EventSubscriber\Admin1FieldSubscriber;
use App\Geonames\UI\Form\EventSubscriber\Admin2FieldSubscriber;
use App\Geonames\UI\Form\EventSubscriber\Admin3FieldSubscriber;
use App\Geonames\UI\Form\EventSubscriber\CountryFieldSubscriber;
use App\Organisation\UI\Form\EventSubscriber\OrganisationFieldSubscriber;
use App\Shared\UI\Form\FormFields\AbstractFormFields;
use App\Shared\UI\Form\FormFields\AdministrativeDivisionSubscriberTrait;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class PersonFields extends AbstractFormFields
{
    use AdministrativeDivisionSubscriberTrait;


    public function __construct()
    {

        $this->fields= [
            ['email', EmailType::class, [
                'label'=>'Organisation code',
                'required'=>false,
                'attr'=>[
                    'class'=>'js-email',
                    'field_id'=>494
                ]]
            ],
            ['surname', null, ['label'=>'','attr'=>['field_id'=>479]]],
            ['name', null, [
                'label'=>'Name',
                'required'=>true,
                'attr'=>['field_id'=>480]]],
            ['title', null, ['label'=>'','attr'=>['field_id'=>483]]],
            ['middleinitial', null, ['label'=>'','attr'=>['field_id'=>481]]],
            ['initialforgivennames', null, ['label'=>'','attr'=>['field_id'=>482]]],
            ['addressline1', null, ['label'=>'','attr'=>['field_id'=>484]]],
            ['addressline2', null, ['label'=>'','attr'=>['field_id'=>485]]],
            ['addressline3', null, ['label'=>'','attr'=>['field_id'=>486]]],
            ['addressline4', null, ['label'=>'','attr'=>['field_id'=>487]]],
            ['cityorsuburb', null, ['label'=>'','attr'=>['field_id'=>488]]],
            ['phoneprefix', null, ['label'=>'','attr'=>['field_id'=>495]]],
            ['homephonenumber', null, ['label'=>'','attr'=>['field_id'=>496]]],
            ['workphonenumber', null, ['label'=>'','attr'=>['field_id'=>497]]],
            ['mobilephonenumber', null, ['label'=>'','attr'=>['field_id'=>498]]],
            ['faxphonenumber', null, ['label'=>'','attr'=>['field_id'=>499]]],
            ['pagerphonenumber', null, ['label'=>'','attr'=>['field_id'=>500]]],
            ['postcode', null, ['label'=>'','attr'=>['field_id'=>491]]]

        ];

        $this->subscribers= [
            [ 'country', CountryFieldSubscriber::class, ['options'=>[
                'label'=>'',
                'attr'=>['field_id'=>493]
            ]]],
            ['admin1',Admin1FieldSubscriber::class, ['options'=>[
                'label'=>'State code',
                'attr'=>['field_id'=>490]
            ]]],
            ['admin2',Admin2FieldSubscriber::class],
            ['admin3',Admin3FieldSubscriber::class],
            [ 'organisation', OrganisationFieldSubscriber::class, ['options'=>[
                'label'=>'',
                'attr'=>['field_id'=>501]
            ]]],
            [ 'organisation2', OrganisationFieldSubscriber::class, [
                'name'=>'organisation2',
                'options'=>[
                    'label'=>'',
                    'attr'=>['field_id'=>502]
                ]
            ]],
            [ 'organisation3', OrganisationFieldSubscriber::class, [
                'name'=>'organisation3',
                'options'=>[
                    'label'=>'',
                    'attr'=>['field_id'=>503]
                ]
            ]]
        ];
    }
}