<?php
namespace App\Form\backend\Publication;
use App\Form\EventListener\AddAdmin1FieldSubscriber;
use App\Form\EventListener\AddCountryFieldSubscriber;
use App\Form\EventListener\AddLinkFieldSubscriber;
use App\Form\FormFields\AbstractFormFields;
use App\Form\FormFields\AdministrativeDivisionSubscriberTrait;

class PublicationFields extends AbstractFormFields
{
 use AdministrativeDivisionSubscriberTrait;
    public static function getFields(): array
    {
        return [
            ['publicationyear', null, ['label'=>'','attr'=>['code_id'=>10308]]],
            ['publicationyearsuffix', null, ['label'=>'','attr'=>['code_id'=>309]]],
            ['volumenumber', null, ['label'=>'','attr'=>['code_id'=>10312]]],
            ['issuenumber', null, ['label'=>'','attr'=>['code_id'=>10313]]],
            ['bookpublisherandcity', null, ['label'=>'','attr'=>['code_id'=>314]]],
            ['pagerange', null, ['label'=>'','attr'=>['code_id'=>10315]]],
            ['authororeditor', null, ['label'=>'','attr'=>['code_id'=>10310]]],
            ['isbn', null, ['label'=>'','attr'=>['code_id'=>608]]],
            ['issn', null, ['label'=>'','attr'=>['code_id'=>320]]],
            ['publicationname', null, ['label'=>'','attr'=>['code_id'=>10311]]],
            ['legaldepositnumber', null, ['label'=>'','attr'=>['code_id'=>10608]]],
            ['description', null, ['label'=>'','attr'=>['code_id'=>10609]]],
            ['content', null, ['label'=>'','attr'=>['code_id'=>10610]]],
            ['url', null, ['label'=>'','attr'=>['code_id'=>1008]]]

        ];
    }


    public static function getSubscribers():array
    {
        return [
            [ 'country', AddCountryFieldSubscriber::class, ['options'=>[
                'label'=>'',
                'required' => true,
                'attr'=>['code_id'=>10319]
            ]]],
            ['admin1',AddAdmin1FieldSubscriber::class, ['options'=>[
                'label'=>'',
                'attr'=>['code_id'=>10321]
            ]]],
            ['link',AddLinkFieldSubscriber::class, ['options'=>[
                'label'=>'',
                'attr'=>['code_id'=>10320]
            ]]]
        ];
    }
}