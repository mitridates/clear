<?php
namespace App\Fielddefinition\UI\Form\EventSubscriber;

use App\Fielddefinition\Domain\Entity\Fielddefinition;
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
 * $builder->addEventSubscriber(new AddFielddefinitionFieldSubscriber( $factory,  array(
 *          'name'=>'excluded',//property/field name
 *          'getMethod'=>'getExcluded',//Get method for property if mapped
 *          'options'=>array() //ChoiceType Field options
 *      )) );
 */
class FielddefinitionFieldSubscriber implements EventSubscriberInterface
{
    /**
     * @var FormFactoryInterface
     */
    private $factory;
    /**
     * @var array
     */
    private $fielddefinition;

    public function __construct(FormFactoryInterface $factory, array $fielddefinition=[])
    {
        $this->factory = $factory;
        $this->fielddefinition= array_replace_recursive(
            array(
                'name'=>'definition',//field name/property if mapped
                'getMethod'=>'getDefinition',//method if mapped
                'options'=>array(
                    'placeholder' => 'select.fielddefinition',
                    'translation_domain' => 'cavemessages',
                    'attr'=>array(
                        'class'=>'js-fielddefinition jsuggest-select'
                    )
                )
            ),
            $fielddefinition);
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
     * @param string|Fielddefinition|null $fielddefinition
     */
    private function addFielddefinitionForm($form, $fielddefinition)
    {
        $options = array_merge(
            array(
                'auto_initialize' => false,
                'attr'          => [],
                'class'=> Fielddefinition::class,
                'required' => false,
                'placeholder'   => 'select.fielddefinition',
                'translation_domain' => 'cavemessages',
                'label'=>'fielddefinition',//cavemessages.yml
                'choice_label' => 'value',
                'choice_value'=>'code',
                'query_builder' => function (EntityRepository $repository) use ($fielddefinition) {
                    return $repository->createQueryBuilder('o')
                        ->where('o.code = :fielddefinition')
                        ->setParameter('fielddefinition', $fielddefinition);
                }
            ),$this->fielddefinition['options']
        );

        $form->add($this->factory->createNamed(
            $this->fielddefinition['name'],
            EntityType::class,
            $fielddefinition,
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
        if(! $this->fielddefinition['getMethod']?? false){
            $this->addFielddefinitionForm($form, NULL, NULL);
            return;
        }

        $fvcOrNull = (null!== $this->fielddefinition) ? $data->{$this->fielddefinition['getMethod']}() : null;
        $this->addFielddefinitionForm($form, $fvcOrNull);
    }

    /**
     * @param FormEvent $event
     */
    public function preSubmit(FormEvent $event)
    {
        $data = $event->getData();
        if (null === $data) return;
        $form = $event->getForm();
        $fvcOrNull=  $data[$this->fielddefinition['name']] ?? null;

        $this->addFielddefinitionForm($form, $fvcOrNull);
    }
}