<?php

namespace App\Form\backend\Map\FormTypeFields;

use App\Entity\FieldDefinition\Fieldvaluecode;
use App\Form\EventListener\AddAdmin1FieldSubscriber;
use App\Form\EventListener\AddAdmin2FieldSubscriber;
use App\Form\EventListener\AddAdmin3FieldSubscriber;
use App\Form\EventListener\AddAreaFieldSubscriber;
use App\Form\EventListener\AddCountryFieldSubscriber;
use App\Form\EventListener\AddMapserieFieldSubscriber;
use App\Form\EventListener\AddOrganisationFieldSubscriber;
use App\Form\EventListener\AddPersonFieldSubscriber;
use App\Form\FormFields\AbstractFormFields;
use App\Form\FormFields\AdministrativeDivisionSubscriberTrait;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormFactoryInterface;

class MapFields extends AbstractFormFields
{
    use AdministrativeDivisionSubscriberTrait;

    public function __construct()
    {
        $this->fields= [
            ['name', null, ['required'=>true,'attr'=>['field_id'=>202]]],
            ['subsheetname', null, ['attr'=>['field_id'=>272]]],
            ['scale', null, ['attr'=>['field_id'=>205]]],
            ['type', EntityType::class, [
                'class'=>Fieldvaluecode::class,
                'attr'=>array('field_id'=>367, 'style'=>'width:100%'),
                'required' => false,//show empty option
                'choice_label' => 'value',
                'choice_value'=>'id',
                'query_builder' => function(EntityRepository $e){
                    return $e->createQueryBuilder('f')
                        ->select('f')
                        ->where('f.field = :field')
                        ->orderBy('f.value', 'ASC')
                        ->setParameter('field', 367);
                }
            ]],
            ['edition', null, ['attr'=>['field_id'=>557]]],
            ['number', null, ['attr'=>['field_id'=>271]]],



            ['surveygradevalue', null, ['attr'=>['field_id'=>204]]],
            ['surveystartyear', null, ['attr'=>['field_id'=>607]]],
            ['surveyfinishyear', null, ['attr'=>['field_id'=>207]]],
            ['latestupdateyear', null, ['attr'=>['field_id'=>273]]],

            ['geogcoordsshown', null, ['attr'=>['field_id'=>554]]],
            ['geodeticdatum', null, ['attr'=>['field_id'=>551]]],
            ['heightdatum', null, ['attr'=>['field_id'=>552]]],
            ['grid', null, ['attr'=>['field_id'=>553]]],


            ['northlatitude', null, ['attr'=>['field_id'=>274]]],
            ['southlatitude', null, ['attr'=>['field_id'=>275]]],
            ['eastlongitude', null, ['attr'=>['field_id'=>276]]],
            ['westlongitude', null, ['attr'=>['field_id'=>277]]],


            ['sourceifnoid', null, ['attr'=>['field_id'=>209]]],
            ['sourcetype', EntityType::class, [
                'class'=>Fieldvaluecode::class,
                'attr'=>array('field_id'=>396, 'style'=>'width:100%'),
                'required' => false,//show empty option
                'choice_label' => 'value',
                'choice_value'=>'id',
                'query_builder' => function(EntityRepository $e){
                    return $e->createQueryBuilder('f')
                        ->select('f')
                        ->where('f.field = :field')
                        ->orderBy('f.value', 'ASC')
                        ->setParameter('field', 623);
                }
            ]]
        ];

        $this->subscribers= [
            [ 'country', AddCountryFieldSubscriber::class, ['options'=>[
                'attr'=>['field_id'=>196]
            ]]],
            [ 'sourcecountry', AddCountryFieldSubscriber::class, [
                'name'=>'sourcecountry',
                'getMethod'=>'getSourcecountry',
                'options'=>[
                    'attr'=>[
                        'field_id'=>370
                    ]]
            ]],
            ['admin1',AddAdmin1FieldSubscriber::class, ['options'=>[
                'attr'=>['field_id'=>197]
            ]]],
            ['admin2',AddAdmin2FieldSubscriber::class, []],
            ['admin3',AddAdmin3FieldSubscriber::class, []],
            ['mapserie',AddMapserieFieldSubscriber::class, ['options'=>['attr'=>['field_id'=>366]]]],
            ['area', AddAreaFieldSubscriber::class,['options' => ['attr' => ['field_id' => 198]]]],
            ['maphostarea', AddAreaFieldSubscriber::class,['options' => ['attr' => ['field_id' => 211]]]],

            ['sourceorg', AddOrganisationFieldSubscriber::class,[
                'options' => ['attr' => ['field_id' => 200]],
                'name'=>'sourceorg',
                'getMethod'=>'getSourceorg',
            ]
            ],
            ['principalsurveyorid',AddPersonFieldSubscriber::class, [
                'name'=>'principalsurveyorid',
                'options'=>['attr'=>['field_id'=>208]]
            ]],
            ['principaldrafterid',AddPersonFieldSubscriber::class, [
                'name'=>'principaldrafterid',
                'options'=>['attr'=>['field_id'=>402]]
            ]],
            ['surveygradeorg',AddOrganisationFieldSubscriber::class, [
                'name'=>'surveygradeorg',
                'options'=>['attr'=>['field_id'=>203]]
            ]]
        ];
    }
    public static function deletePostSubmitData(FormEvent $event, array $fields): void
    {
        $form= $event->getForm();
        foreach ($fields as $field) {
            $form->get($field)->setData(null);
        }
    }
}