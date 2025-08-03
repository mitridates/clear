<?php

namespace App\Mapserie\UI\Form;

use App\Fielddefinition\Domain\Entity\Fieldvaluecode;
use App\Form\EventListener\AddOrganisationFieldSubscriber;
use App\Form\FormFields\AbstractFormFields;
use App\Form\FormFields\AdministrativeDivisionSubscriberTrait;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class MapserieFields extends AbstractFormFields
{
 use AdministrativeDivisionSubscriberTrait;

 public function __construct()
 {
     $this->fields=[
         ['name', null, [
             'label'=>'',
             'required' => true,
             'attr'=>['field_id'=>279]]
         ],
         ['comment', null, [
             'label'=>'',
             'attr'=>['field_id'=>10374]]
         ],
         ['code', null, ['label'=>'','attr'=>['field_id'=>278]]],
         ['abbreviation', null, ['label'=>'','attr'=>['field_id'=>372]]],
         ['scale', null, ['label'=>'','required' => true,'attr'=>['field_id'=>373]]],
         ['lengthunits', EntityType::class, [
             'class'=>Fieldvaluecode::class,
             'attr'=>['field_id'=>280],
             'label'=>'Length units',
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
         ['maptype', EntityType::class, [
             'class'=>Fieldvaluecode::class,
             'attr'=>['field_id'=>559],
             'label'=>'Organisation coverage',
             'required' => false,//show empty option
             'choice_label' => 'value',
             'choice_value'=>'id',
             'query_builder' => function(EntityRepository $e){
                 return $e->createQueryBuilder('f')
                     ->select('f')
                     ->where('f.field = :field')
                     ->orderBy('f.value', 'ASC')
                     ->setParameter('field', 367);
             },
         ]]
     ];
     $this->subscribers=[
         ['publisher', AddOrganisationFieldSubscriber::class,
             [
                 'name'=>'publisher',
                 'getMethod'=>'getPublisher',
                 'options'=>[
                     'label'=>'',
                     'attr'=>['field_id'=>374]]
             ]
         ]
     ];
 }
}