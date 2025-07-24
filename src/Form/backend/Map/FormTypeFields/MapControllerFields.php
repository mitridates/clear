<?php

namespace App\Form\backend\Map\FormTypeFields;

use App\Entity\Map\Mapcontroller;
use App\Form\EventListener\AddOrganisationFieldSubscriber;
use App\Form\EventListener\AddPersonFieldSubscriber;
use App\Form\FormFields\AbstractFormFields;
use App\Form\FormFields\AdministrativeDivisionSubscriberTrait;
use App\Shared\reflection\EntityReflectionHelper;
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