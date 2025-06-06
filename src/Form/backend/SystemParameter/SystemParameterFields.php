<?php

namespace App\Form\backend\SystemParameter;

use App\Entity\FieldDefinition\Fieldvaluecode;
use App\Form\EventListener\AddCountryFieldSubscriber;
use App\Form\EventListener\AddOrganisationFieldSubscriber;
use App\Form\FormFields\AbstractFormFields;
use App\Form\FormFields\SubscribersTrait;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class SystemParameterFields extends AbstractFormFields
{

    use SubscribersTrait;
    public function __construct($options)
    {
        $this->fields=[
            //textType
            ['name', null, ['label'=>'System parameter name','attr'=>['field_id'=>0]]],
            ['active', null, ['label'=>'Active','attr'=>['field_id'=>0]]],
            ['altitudeprecision', null, ['label'=>'Coarse altitude precision','attr'=>['field_id'=>526]]],
            ['altitudeunit', null, ['label'=>'','attr'=>['field_id'=>0]]],
            ['country', null, ['label'=>'','attr'=>['field_id'=>0]]],
            ['organisationsite', null, ['label'=>'','attr'=>['field_id'=>0]]],
            ['organisationdbm', null, ['label'=>'','attr'=>['field_id'=>0]]],
            ['geodeticdatum', null, ['label'=>'Geodetic Datum used','attr'=>['field_id'=>288]]],
            ['mapgrid', null, ['label'=>'Map Grid used','attr'=>['field_id'=>289]]],
            ['heightdatum', null, ['label'=>'Height Datum used','attr'=>['field_id'=>516]]],
            ['mapserie', null, ['label'=>'Coarse - map series','attr'=>['field_id'=>412]]],
            ['refunits', null, ['label'=>'','attr'=>['field_id'=>0]]],
            ['grrefqualifier', null, ['label'=>'Coarse - gr.ref qualifier','attr'=>['field_id'=>413]]],
            ['geogprecision', null, ['label'=>'Coarse - geogr. precision','attr'=>['field_id'=>294]]],
            ['grefprecision', null, ['label'=>'Coarse - gr.ref precision','attr'=>['field_id'=>295]]],
            ['landunit', null, ['label'=>'Coarse - land unit name','attr'=>['field_id'=>440]]],
            ['softwarelevel', null, ['label'=>'Software upgrade level','attr'=>['field_id'=>529]]],
            ['mapdir', null, ['label'=>'Map images directory path','attr'=>['field_id'=>596]]],
            ['topodir', null, ['label'=>'','attr'=>['field_id'=>13079]]],
            ['transcodes', null, ['label'=>'Code translations Y/N','attr'=>['field_id'=>291]]],
            ['version', null, ['label'=>'Program version number','attr'=>['field_id'=>539]]],
            //choiceType
            ['language', choiceType::class, ['choices'=> array_flip($options['locales']), 'label'=>'','attr'=>['field_id'=>158]]],
            //EntityType
            ['refunits', EntityType::class, [
                'class'=>Fieldvaluecode::class,
                'attr'=>['field_id'=>296],
                'label'=>'Coarse - grid ref units',
                'required' => false,//show empty option
                'choice_label' => 'value',
                'choice_value'=>'id',
                'query_builder' => function(EntityRepository $e){
                    return $e->createQueryBuilder('f')
                        ->select('f')
                        ->where('f.field = :field')
                        ->orderBy('f.value', 'ASC')
                        ->setParameter('field', 298);
                },
            ]],
            ['altitudeunit', EntityType::class, [
                'class'=>Fieldvaluecode::class,
                'attr'=>['field_id'=>446],
                'label'=>'Coarse - altitude units',
                'required' => false,//show empty option
                'choice_label' => 'value',
                'choice_value'=>'id',
                'query_builder' => function(EntityRepository $e){
                    return $e->createQueryBuilder('f')
                        ->select('f')
                        ->where('f.field = :field')
                        ->orderBy('f.value', 'ASC')
                        ->setParameter('field', 298);
                },
            ]]

        ];

        $this->subscribers=[
            [
                'country',
                AddCountryFieldSubscriber::class,
                [
                    'options'=>[
                        'required' => true,
                        'attr'=>['field_id'=>156]]
                ]
            ],
            [
                'getOrganisationdbm',
                AddOrganisationFieldSubscriber::class,
                ['name'=>'organisationdbm',
                    'getMethod'=>'getOrganisationdbm',
                    'options'=>[
                        'label'=>'Database org',
                        'required' => true,
                        'attr'=>['field_id'=>182]]
                ]
            ],
            [
                'getOrganisationsite',
                AddOrganisationFieldSubscriber::class,
                [
                    'name'=>'organisationsite',
                    'getMethod'=>'getOrganisationsite',
                    'options'=>[
                        'label'=>'Site organisation',
                        'attr'=>['field_id'=>157]]
                ]
            ]
        ];
    }

}