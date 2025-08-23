<?php
namespace App\Citation\UI\Form\EventSubscriber;

use App\Citation\Domain\Entity\Citation;
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
  * $citation = array(
  *      'name'=>'citation',//property/field name
  *      'getMethod'=>'getCitation',//Get method for property if mapped
  *      'options'=>array() //ChoiceType Field options
  *  );
  *
  *
  * $builder->addEventSubscriber(new AddCitationFieldSubscriber($factory, $citation) );
  */
class CitationFieldSubscriber implements EventSubscriberInterface
{
    /**
     * @var FormFactoryInterface
     */
    private $factory;
    /**
     * @var array
     */
    private $citation;

    public function __construct(FormFactoryInterface $factory, array $citation=[])
    {
        $this->factory = $factory;
        $this->citation= array_replace_recursive(
            array(
                'name'=>'citation',//field name/property if mapped
                'getMethod'=>'getCitation',//method if mapped
                'options'=>array(
                    'placeholder' => 'select.citation',
                    'translation_domain' => 'cavemessages',
                    'attr'=>array(
                        'class'=>'js-citation jsuggest-select'
                    )
                )
            ),
            $citation);
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
     * @param string|Citation|null $citation
     */
    private function addCitationForm($form, $citation)
    {
        $options = array_merge(
            array(
                'auto_initialize' => false,
                'attr' => array(
                ),
                'class'=> Citation::class,
                'required' => false,
                'translation_domain' => 'cavemessages',
                'placeholder'   => 'select.citation',
                'label'=>'Citation',
                'choice_label' => function($data){
                    /** @var Citation $data*/
                    $ret= $data->getTitle();
                    if($data->getSubtitle()) $ret.=': '.$data->getSubtitle();
                    return $ret;
                },
                'choice_value'=>'id',
                'query_builder' => function (EntityRepository $repository) use ($citation) {
                    return $repository->createQueryBuilder('o')
                            ->where('o.id = :citation')
                            ->setParameter('citation', $citation);
                }
            ),$this->citation['options']
        );

       $form->add($this->factory->createNamed(
           $this->citation['name'] ,
           EntityType::class,
           $citation,
           $options
       ));
    }

    /**
     * @return array
     */
    public function getCitation(): array
    {
        return $this->citation;
    }

    /**
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event): void
    {
        $data = $event->getData();
        if (null === $data) return;

        $this->addCitationForm($event->getForm(), $data->{$this->citation['getMethod']}() ?? null);
    }

    /**
     * @param FormEvent $event
     */
    public function preSubmit(FormEvent $event): void
    {
        $data = $event->getData();
        if (null === $data) return;

        $this->addCitationForm($event->getForm(), $data[$this->citation['name']] ?? null);
    }

}