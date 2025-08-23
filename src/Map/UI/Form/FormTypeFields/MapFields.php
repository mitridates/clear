<?php

namespace App\Map\UI\Form\FormTypeFields;

use App\Area\UI\Form\EventSubscriber\AreaFieldSubscriber;
use App\Fielddefinition\Domain\Entity\Fieldvaluecode;
use App\Geonames\UI\Form\EventSubscriber\Admin1FieldSubscriber;
use App\Geonames\UI\Form\EventSubscriber\Admin2FieldSubscriber;
use App\Geonames\UI\Form\EventSubscriber\Admin3FieldSubscriber;
use App\Geonames\UI\Form\EventSubscriber\CountryFieldSubscriber;
use App\Mapserie\UI\Form\EventSubscriber\MapserieFieldSubscriber;
use App\Organisation\UI\Form\EventSubscriber\OrganisationFieldSubscriber;
use App\Person\UI\Form\EventSubscriber\PersonFieldSubscriber;
use App\Shared\UI\Form\FormFields\AbstractFormFields;
use App\Shared\UI\Form\FormFields\AdministrativeDivisionSubscriberTrait;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormEvent;

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
            [ 'country', CountryFieldSubscriber::class, ['options'=>[
                'attr'=>['field_id'=>196]
            ]]],
            [ 'sourcecountry', CountryFieldSubscriber::class, [
                'name'=>'sourcecountry',
                'getMethod'=>'getSourcecountry',
                'options'=>[
                    'attr'=>[
                        'field_id'=>370
                    ]]
            ]],
            ['admin1',Admin1FieldSubscriber::class, ['options'=>[
                'attr'=>['field_id'=>197]
            ]]],
            ['admin2',Admin2FieldSubscriber::class, []],
            ['admin3',Admin3FieldSubscriber::class, []],
            ['mapserie',MapserieFieldSubscriber::class, ['options'=>['attr'=>['field_id'=>366]]]],
            ['area', AreaFieldSubscriber::class,['options' => ['attr' => ['field_id' => 198]]]],
            ['maphostarea', AreaFieldSubscriber::class,['options' => ['attr' => ['field_id' => 211]]]],

            ['sourceorg', OrganisationFieldSubscriber::class,[
                'options' => ['attr' => ['field_id' => 200]],
                'name'=>'sourceorg',
                'getMethod'=>'getSourceorg',
            ]
            ],
            ['principalsurveyorid',PersonFieldSubscriber::class, [
                'name'=>'principalsurveyorid',
                'options'=>['attr'=>['field_id'=>208]]
            ]],
            ['principaldrafterid',PersonFieldSubscriber::class, [
                'name'=>'principaldrafterid',
                'options'=>['attr'=>['field_id'=>402]]
            ]],
            ['surveygradeorg',OrganisationFieldSubscriber::class, [
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