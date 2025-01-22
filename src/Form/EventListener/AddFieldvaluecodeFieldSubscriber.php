<?php
namespace App\Form\EventListener;

use App\Entity\FieldDefinition\Fieldvaluecode;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

/**
 * Set form field on page load or submit if selected value exists.
 * @package App\EventListener\Form
 *
 * @example
 * $builder->addEventSubscriber(new AddFieldvaluecodeFieldSubscriber( $factory,  array(
 *          'name'=>'excluded',//property/field name
 *          'getMethod'=>'getExcluded',//Get method for property if mapped
 *          'options'=>array() //ChoiceType Field options
 *      )) );
 */
class AddFieldvaluecodeFieldSubscriber implements EventSubscriberInterface
{
    /**
     * @var FormFactoryInterface
     */
    private $factory;
    /**
     * @var array
     */
    private $fieldvaluecode;

    public function __construct(FormFactoryInterface $factory, array $fieldvaluecode=[])
    {
        $this->factory = $factory;
        $this->fieldvaluecode= array_replace_recursive(
            array(
                'name'=>'valuecode',//field name/property if mapped
                'getMethod'=>'getValuecode',//method if mapped
                'options'=>array(
                    'placeholder' => 'select.fieldvaluecode',
                    'translation_domain' => 'cavemessages',
                    'attr'=>array(
                        'class'=>'js-fieldvaluecode'
                    )
                )
            ),
            $fieldvaluecode);
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array
    {
        return array(
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_SUBMIT   => 'preSubmit',
        );
    }

    /**
     * @param FormInterface $form
     * @param string|Fieldvaluecode|null $fieldvaluecode
     */
    private function addFieldvaluecodeForm($form, $fieldvaluecode)
    {
        $options = array_merge(
            array(
                'auto_initialize' => false,
                'attr'          => [],
                'class'=> Fieldvaluecode::class,
                'required' => false,
                'placeholder'   => 'select.fieldvaluecode',
                'translation_domain' => 'cavemessages',
                'label'=>'fieldvaluecode',//cavemessages.yml
                'choice_label' => 'value',
                'choice_value'=>'id',
                'query_builder' => function (EntityRepository $repository) use ($fieldvaluecode) {
                    return $repository->createQueryBuilder('o')
                        ->where('o.id = :fieldvaluecode')
                        ->setParameter('fieldvaluecode', $fieldvaluecode);
                }
            ),$this->fieldvaluecode['options']
        );

        $form->add($this->factory->createNamed(
            $this->fieldvaluecode['name'],
            EntityType::class,
            $fieldvaluecode,
            $options
        ));
    }

    /**
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        $data = $event->getData();
        if (null === $data) return;
        $form = $event->getForm();
        if(! $this->fieldvaluecode['getMethod']?? false){
            $this->addFieldvaluecodeForm($form, NULL, NULL);
            return;
        }

        $fvcOrNull = (null!== $this->fieldvaluecode) ? $data->{$this->fieldvaluecode['getMethod']}() : null;
        $this->addFieldvaluecodeForm($form, $fvcOrNull);
    }

    /**
     * @param FormEvent $event
     */
    public function preSubmit(FormEvent $event)
    {
        $data = $event->getData();
        if (null === $data) return;
        $form = $event->getForm();
        $fvcOrNull=  $data[$this->fieldvaluecode['name']] ?? null;

        $this->addFieldvaluecodeForm($form, $fvcOrNull);
    }
}