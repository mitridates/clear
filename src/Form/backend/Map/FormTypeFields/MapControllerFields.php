<?php

namespace App\Form\backend\Map\FormTypeFields;

use App\Entity\FieldDefinition\Fieldvaluecode;
use App\Entity\Map\Mapcontroller;
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
use App\Form\FormFields\FormFieldsEventInterface;
use App\Utils\reflection\EntityReflectionHelper;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class MapControllerFields extends AbstractFormFields
{
    use AdministrativeDivisionSubscriberTrait;

    public function __construct()
    {

        $this->fields= [
            ['comment', null, ['attr'=>['field_id'=>408]]],
            ['controllerifnoid', null, ['attr'=>['field_id'=>210]]],
        ];

        $this->subscribers= [
            ['organisation',AddOrganisationFieldSubscriber::class, [
                'options'=>['attr'=>['field_id'=>406]]
            ]],
            ['person',AddPersonFieldSubscriber::class, [
                'options'=>['attr'=>['field_id'=>407]]
            ]]
        ];
    }

    /**
     * @param Mapcontroller $entity
     * @param ExecutionContextInterface $context
     */
    public function constraints(Mapcontroller $entity, ExecutionContextInterface $context): void
    {
        $count= 0;
        $i=null;
        $props= ['organisation', 'person', 'controllerifnoid'];

        if(EntityReflectionHelper::isEmpty($entity,['map', 'created', 'updated'])){
            $context->buildViolation('form.emptynotallow')
                ->setParameter('code', 100)
                ->addViolation();
        }

        foreach ($props as $index=>$prop){
            if(!EntityReflectionHelper::isNull($entity, $prop)){
                $count++;
                $i= $index;
            }
        }

        if($count>1){
            $context->buildViolation('form.multiplenotallow')
                ->atPath($props[$i])
                ->addViolation();
        }
    }
}