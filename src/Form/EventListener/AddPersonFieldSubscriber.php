<?php
namespace App\Form\EventListener;
use App\Person\Domain\Entity\Person;
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
 */
class AddPersonFieldSubscriber implements EventSubscriberInterface
{
    /**
     * @var FormFactoryInterface
     */
    private $factory;
    /**
     * @var array
     */
    private $person;

    public function __construct(FormFactoryInterface $factory, array $person=[])
    {
        $this->factory = $factory;
        $person['name'] = $person['name'] ?? 'person';
        $person['getMethod']= $person['getMethod'] ?? sprintf('get%s', ucfirst($person['name']));

        $this->person = array_replace_recursive(
            [
                'options'=>array(
                    'placeholder'   => 'select.person',
                    'translation_domain' => 'cavemessages',
                    'attr'=>[ 'class'=>'js-person jsuggest-select']
                )
            ],
            $person
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
     * @param string|Person|null $person
     */
    private function addPersonForm($form, $person)
    {
        $options = array_merge(
            array(
                'auto_initialize' => false,
                'attr'          => array(),
                'translation_domain' => 'cavemessages',
                'placeholder'   => 'select.person',
                'class'=> Person::class,
                'required' => false,
//                'choice_label' => function($person){
//                    /** @var Person $person*/
//                    $val= null;
//                    if($person->getCityorsuburb()){
//                        $val= '. '.$person->getCityorsuburb();
//                    }else{
//                        foreach (['admin1','admin2','admin3','country'] as $l){
//                            $fn='get'.ucfirst(strtolower($l));
//                            if($person->$fn()){
//                                $val= '. '.$person->$fn()->getName();
//                                break;
//                            }
//                        }
//                    }
//                    return $person->getName() . ' ' . $person->getSurname() . $val;
//                },
                'label'=>'Person',
                'choice_label' => function ($person) {
                    return $person->getName() . ' ' . $person->getSurname();
                },
                'choice_value'=>'id',
                'query_builder' => function (EntityRepository $repository) use ($person) {
                    return $repository->createQueryBuilder('m')
                           ->where('m.id = :person')
                          ->setParameter('person', $person);
                    }

            ),$this->person['options'] ?? []
        );

        $form->add($this->factory->createNamed(
            $this->person['name'],
            EntityType::class,
            $person,
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
        $this->addPersonForm($event->getForm(), $data->{$this->person['getMethod']}() ?? null);
    }

    /**
     * @param FormEvent $event
     */
    public function preSubmit(FormEvent $event)
    {
        $data = $event->getData();
        if (null === $data) return;

        $this->addPersonForm($event->getForm(), $data[$this->person['name']] ?? null);
    }
}