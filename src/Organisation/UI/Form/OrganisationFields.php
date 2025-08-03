<?php

namespace App\Organisation\UI\Form;

use App\Fielddefinition\Domain\Entity\Fieldvaluecode;
use App\Form\EventListener\AddAdmin1FieldSubscriber;
use App\Form\EventListener\AddAdmin2FieldSubscriber;
use App\Form\EventListener\AddAdmin3FieldSubscriber;
use App\Form\EventListener\AddCountryFieldSubscriber;
use App\Form\EventListener\AddOrganisationFieldSubscriber;
use App\Form\FormFields\AbstractFormFields;
use App\Form\FormFields\AdministrativeDivisionSubscriberTrait;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class OrganisationFields extends AbstractFormFields
{
 use AdministrativeDivisionSubscriberTrait;

    public function __construct()
    {
        $this->fields=
            [
                //textType
                ['code', null, ['label'=>'Organisation code','attr'=>['field_id'=>178]]],
                ['defunct', null, ['label'=>'Organisation defunct Y/N','attr'=>['field_id'=>382]]],
                ['defunctyear', null, ['label'=>'Final year if defunct','attr'=>['field_id'=>383]]],
                ['postcode', null, ['label'=>'Postcode','attr'=>['field_id'=>378]]],
                ['postcodefist', null, ['label'=>'Postcode first Y/N','attr'=>['field_id'=>379]]],
                ['addressline0', null, ['label'=>'Organisation address line','attr'=>['field_id'=>385]]],
                ['addressline1', null, ['label'=>'Address line 1','attr'=>['field_id'=>386]]],
                ['addressline2', null, ['label'=>'Address line 2','attr'=>['field_id'=>387]]],
                ['addressline3', null, ['label'=>'Address line 3','attr'=>['field_id'=>388]]],
                ['addressline4', null, ['label'=>'Address line 4','attr'=>['field_id'=>389]]],
                ['initials', null, ['label'=>'Organisation initials','attr'=>['field_id'=>390]]],
                ['name', null, ['label'=>'Organisation name','attr'=>['field_id'=>391]]],
                ['email', null, ['label'=>'Org contact email address','attr'=>['field_id'=>614]]],
                ['webpage', null, ['label'=>'Org web page address','attr'=>['field_id'=>615]]],
                //checkbox
                ['isgenerator', CheckboxType::class, [
                    'label'=>'',
                    'required' => false,//default false
                    'attr'=>['field_id'=>10002]]
                ],
//                EntityType
                ['type', EntityType::class, [
                'class'=>Fieldvaluecode::class,
                'attr'=>['field_id'=>381],
                'label'=>'Organisation type',
                'required' => false,//show empty option
                'choice_label' => 'value',
                'choice_value'=>'id',
                'query_builder' => function(EntityRepository $e){
                    return $e->createQueryBuilder('f')
                        ->select('f')
                        ->where('f.field = :field')
                        ->orderBy('f.value', 'ASC')
                        ->setParameter('field', 381);
                },
            ]],
        ['coverage', EntityType::class, [
            'class'=>Fieldvaluecode::class,
            'attr'=>['field_id'=>393],
            'label'=>'Organisation coverage',
            'required' => false,//show empty option
            'choice_label' => 'value',
            'choice_value'=>'id',
            'query_builder' => function(EntityRepository $e){
                return $e->createQueryBuilder('f')
                    ->select('f')
                    ->where('f.field = :field')
                    ->orderBy('f.value', 'ASC')
                    ->setParameter('field', 393);
            },
        ]],
        ['grouping', EntityType::class, [
            'class'=>Fieldvaluecode::class,
            'attr'=>['field_id'=>394],
            'label'=>'Organisation grouping',
            'required' => false,//show empty option
            'choice_label' => 'value',
            'choice_value'=>'id',
            'query_builder' => function(EntityRepository $e){
                return $e->createQueryBuilder('f')
                    ->select('f')
                    ->where('f.field = :field')
                    ->orderBy('f.value', 'ASC')
                    ->setParameter('field', 394);
            },
        ]]
        ];

        $this->subscribers= [
            [ 'country', AddCountryFieldSubscriber::class, ['options'=>[
                'label'=>'Organisation country code',
                'required' => true,
                'attr'=>['field_id'=>376]
            ]]],
            ['admin1',AddAdmin1FieldSubscriber::class, ['options'=>[
                'label'=>'State code',
                'attr'=>['field_id'=>377]
            ]]],
            ['admin2',AddAdmin2FieldSubscriber::class],
            ['admin3',AddAdmin3FieldSubscriber::class],
            [ 'countryaddress', AddCountryFieldSubscriber::class, [
                'name'=>'countryaddress',
                'options'=>[
                    'label'=>'Address country code',
                    'attr'=>['field_id'=>395]
                ]
            ]],
            ['admin1address',AddAdmin1FieldSubscriber::class, [
                'name'=>'admin1address',
                'options'=>[
                    'label'=>'State code',
                    'attr'=>['field_id'=>377]
                ]
            ]],
            ['admin2address',AddAdmin2FieldSubscriber::class, [
                'name'=>'admin2address'
            ]],
            ['admin3address',AddAdmin3FieldSubscriber::class, [
                'name'=>'admin3address'
            ]],
            ['currentidifdefunct', AddOrganisationFieldSubscriber::class,
                ['name'=>'currentidifdefunct',
                    'options'=>[
                        'label'=>'Current org ID if defunct',
                        'attr'=>['field_id'=>384]]
                ]
            ]
        ];
    }

}