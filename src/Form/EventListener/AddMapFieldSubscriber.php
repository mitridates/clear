<?php
namespace App\Form\EventListener;
use App\Entity\Map\Map;
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
 * $fieldvaluecode = array(
 *      'name'=>'fieldvaluecode',//property/field name
 *      'getMethod'=>'getFieldvaluecode',//Get method for property if mapped
 *      'options'=>array() //ChoiceType Field options
 *  );
 *
 *
 * $builder->addEventSubscriber(new AddMapFieldSubscriber( $factory, $fieldvaluecode) );
 */
class AddMapFieldSubscriber implements EventSubscriberInterface
{
    /**
     * @var FormFactoryInterface
     */
    private $factory;
    /**
     * @var array
     */
    private $map;

    public function __construct(FormFactoryInterface $factory, array $map=[])
    {
        $this->factory = $factory;
        $this->map= array_replace_recursive(
            array(
                'name'=>'map',
                'getMethod'=>'getMap',
                'options'=>array(
                    'placeholder'   => 'select.map',
                    'translation_domain' => 'cavemessages',
                    'attr'=>array(
                        "class"=>'js-map jsuggest-select'
                    )
                )
            ),
            $map
        );
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
     * @param string|Map|null $map
     */
    private function addMapForm($form, $map)
    {
        $options = array_merge(
            array(
                'auto_initialize' => false,
                'attr'          => array(
                ),
                'class'=> Map::class,
                'required' => false,
                'translation_domain' => 'cavemessages',
                'placeholder'   => 'select.map',
                'label'=>'Map',
                'choice_label' => 'name',
                'choice_value'=>'id',
                'query_builder' => function (EntityRepository $repository) use ($map) {
                    return $repository->createQueryBuilder('o')
                        ->where('o.id = :map')
                        ->setParameter('map', $map);
                }
            ),$this->map['options']
        );

        $form->add($this->factory->createNamed(
           $this->map['name'],
           EntityType::class,
           $map,
           $options
        ));
    }

    /**
     * @return array
     */
    public function getMap(): array
    {
        return $this->map;
    }

    /**
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        $data = $event->getData();
        if (null === $data) return;

        $this->addMapForm($event->getForm(), $data->{$this->map['getMethod']}() ?? null);
    }

    /**
     * @param FormEvent $event
     */
    public function preSubmit(FormEvent $event)
    {
        $data = $event->getData();
        if (null === $data) return;

        $this->addMapForm($event->getForm(), $data[$this->map['name']] ?? null);
    }
}