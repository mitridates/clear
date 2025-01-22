<?php
namespace App\Form\EventListener;
use App\Entity\Cavern\Trait;
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
class AddCaveFieldSubscriber implements EventSubscriberInterface
{
    /**
     * @var FormFactoryInterface
     */
    private $factory;
    /**
     * @var array
     */
    private $cave;

    public function __construct(FormFactoryInterface $factory, array $cave=[])
    {
        $this->factory = $factory;
        $this->cave= array_replace_recursive(
            array(
                'name'=>'cave',//field name/property if mapped
                'getMethod'=>'getCave',//method if mapped
                'options'=>array(
                    'placeholder' => 'select.cave',
                    'translation_domain' => 'cavemessages',
                    'attr'=>array(
                        'class'=>'js-cave jsuggest-select'
                    )
                )
            ),
            $cave
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
     * @param string|Cave|null $cave
     */
    private function addCaveForm($form, $cave)
    {
        $options = array_merge(
            array(
                'auto_initialize' => false,
                'attr' => array(),
                'translation_domain' => 'cavemessages',
                'placeholder'   => 'select.cave',
                'class'=> Cave::class,
                'required' => false,
                'label'=>'Cave',
//                'choice_label' => 'name',
                'choice_label' => function($data){
                    /** @var Cave $data*/
                    $val= null;
                    foreach (['admin1','admin2','admin3','country'] as $l){
                        $fn='get'.ucfirst(strtolower($l));
                        if($data->$fn()){
                            $val= '. '.$data->$fn()->getName();
                            break;
                        }
                    }
                    return $data->getName() . $val;
                },
                'choice_value'=>'id',
                'query_builder' => function (EntityRepository $repository) use ($cave) {
                    return $repository->createQueryBuilder('o')
                        ->where('o.id = :cave')
                        ->setParameter('cave', $cave);
                }
            ),$this->cave['options'] ?? []
        );

        $form->add($this->factory->createNamed(
            $this->cave['name'],
            EntityType::class,
            $cave,
            $options
        ));
    }

    /**
     * @return array
     */
    public function getCave(): array
    {
        return $this->cave;
    }

    /**
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        $data = $event->getData();
        if (null === $data) return;

        $this->addCaveForm($event->getForm(), $data->{$this->cave['getMethod']}() ?? null);
    }

    /**
     * @param FormEvent $event
     */
    public function preSubmit(FormEvent $event)
    {
        $data = $event->getData();
        if (null === $data) return;

        $this->addCaveForm($event->getForm(), $data[$this->cave['name']] ?? null);
    }
}