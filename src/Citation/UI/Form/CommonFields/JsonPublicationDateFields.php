<?php

namespace App\Citation\UI\Form\CommonFields;

use App\Citation\Domain\Entity\Citation;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;

class JsonPublicationDateFields extends JsonDateFields
{
    public function __construct(FormBuilderInterface $builder)
    {
        parent::__construct($builder);
        $form= $builder->getForm();
        /** @var Citation $citation */
        $citation = $form->getData();
        $json= json_decode($citation->getJsondata(), true);
        foreach(['year', 'month', 'day'] as $v){
            if(isset($json[$v])){
                $this->setFieldOptions($v, ['data'=> $json[$v]]);
            }
        }

        $this
            ->setFieldOptions('month', [
                'choices'=>self::getMonthWithSeasonChoices(),
            ])
            ->setFieldOptions('day', [
                'choices'=>self::getDaysInMonthYear($json['month']??null,$json['year']??null),
            ]);
    }

    /**
     *
     * The choices[] option MUST contain the POST value selected if exists or will send a FormError.
     * @param FormEvent $event
     * @return void
     */
    public static function setPreSubmitData(FormEvent $event): void
    {
        $data= $event->getData();
        $form= $event->getForm();
        $add=  ['day', ChoiceType::class, [
            'label'=>'Day',
            'required'=>false,
            'choices' => isset($data['day'])?[$data['day'] => $data['day']]: null,
            'mapped'=>false,
            'data'=>$data['day']??null,
            'placeholder'=>'No day',
            'attr'=>['class'=>'js-day'],
        ]];
        call_user_func_array([$form, 'add'], $add);
    }
    
}