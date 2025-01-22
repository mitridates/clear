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
            ['name', null, ['required'=>true,'attr'=>['code_id'=>202]]],
            ['subsheetname', null, ['attr'=>['code_id'=>272]]],
            ['scale', null, ['attr'=>['code_id'=>205]]],
            ['type', EntityType::class, [
                'class'=>Fieldvaluecode::class,
                'attr'=>array('code_id'=>367, 'style'=>'width:100%'),
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
            ['edition', null, ['attr'=>['code_id'=>557]]],
            ['number', null, ['attr'=>['code_id'=>271]]],



            ['surveygradevalue', null, ['attr'=>['code_id'=>204]]],
            ['surveystartyear', null, ['attr'=>['code_id'=>607]]],
            ['surveyfinishyear', null, ['attr'=>['code_id'=>207]]],
            ['latestupdateyear', null, ['attr'=>['code_id'=>273]]],

            ['geogcoordsshown', null, ['attr'=>['code_id'=>554]]],
            ['geodeticdatum', null, ['attr'=>['code_id'=>551]]],
            ['heightdatum', null, ['attr'=>['code_id'=>552]]],
            ['grid', null, ['attr'=>['code_id'=>553]]],


            ['northlatitude', null, ['attr'=>['code_id'=>274]]],
            ['southlatitude', null, ['attr'=>['code_id'=>275]]],
            ['eastlongitude', null, ['attr'=>['code_id'=>276]]],
            ['westlongitude', null, ['attr'=>['code_id'=>277]]],


            ['sourceifnoid', null, ['attr'=>['code_id'=>209]]],
            ['sourcetype', EntityType::class, [
                'class'=>Fieldvaluecode::class,
                'attr'=>array('code_id'=>396, 'style'=>'width:100%'),
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
                'attr'=>['code_id'=>196]
            ]]],
            [ 'sourcecountry', AddCountryFieldSubscriber::class, [
                'name'=>'sourcecountry',
                'getMethod'=>'getSourcecountry',
                'options'=>[
                    'attr'=>[
                        'code_id'=>370
                    ]]
            ]],
            ['admin1',AddAdmin1FieldSubscriber::class, ['options'=>[
                'attr'=>['code_id'=>197]
            ]]],
            ['admin2',AddAdmin2FieldSubscriber::class, []],
            ['admin3',AddAdmin3FieldSubscriber::class, []],
            ['mapserie',AddMapserieFieldSubscriber::class, ['options'=>['attr'=>['code_id'=>366]]]],
            ['area', AddAreaFieldSubscriber::class,['options' => ['attr' => ['code_id' => 198]]]],
            ['maphostarea', AddAreaFieldSubscriber::class,['options' => ['attr' => ['code_id' => 211]]]],

            ['sourceorg', AddOrganisationFieldSubscriber::class,[
                'options' => ['attr' => ['code_id' => 200]],
                'name'=>'sourceorg',
                'getMethod'=>'getSourceorg',
            ]
            ],
            ['principalsurveyorid',AddPersonFieldSubscriber::class, [
                'name'=>'principalsurveyorid',
                'options'=>['attr'=>['code_id'=>208]]
            ]],
            ['principaldrafterid',AddPersonFieldSubscriber::class, [
                'name'=>'principaldrafterid',
                'options'=>['attr'=>['code_id'=>402]]
            ]],
            ['surveygradeorg',AddOrganisationFieldSubscriber::class, [
                'name'=>'surveygradeorg',
                'options'=>['attr'=>['code_id'=>203]]
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