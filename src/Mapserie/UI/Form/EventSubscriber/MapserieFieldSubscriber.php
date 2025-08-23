<?php
namespace App\Mapserie\UI\Form\EventSubscriber;

use App\Mapserie\Domain\Entity\Mapserie;
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

class MapserieFieldSubscriber implements EventSubscriberInterface
{
    /**
     * @var FormFactoryInterface
     */
    private $factory;
    /**
     * @var array
     */
    private $mapserie;

    public function __construct(FormFactoryInterface $factory, array $mapserie=[])
    {
        $this->factory = $factory;
        $this->mapserie= array_replace_recursive(
            array(
                'name'=>'mapserie',//field name/property if mapped
                'getMethod'=>'getMapserie',//method if mapped
                'options'=>array(
                    'placeholder' => 'select.mapserie',
                    'translation_domain' => 'cavemessages',
                    'attr'=>array(
                        'class'=>'js-mapserie jsuggest-select'
                    )
                )
            ),
            $mapserie);
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
     * @param string|Mapserie|null $mapserie
     */
    private function addMapserieForm($form, $mapserie)
    {
        $options = array_merge(
            array(
                'auto_initialize' => false,
                'attr'          => array(
                ),
                'class'=> Mapserie::class,
                'required' => false,
                'translation_domain' => 'cavemessages',
                'placeholder'   => 'select.mapserie',
                'label'=>'Mapserie',
                'choice_label' => 'name',
                'choice_value'=>'id',
                'query_builder' => function (EntityRepository $repository) use ($mapserie) {
                    return $repository->createQueryBuilder('m')
                           ->where('m.id = :mapserie')
                          ->setParameter('mapserie', $mapserie);
                    }
                
            ),$this->mapserie['options'] ?? []
        );

        $form->add($this->factory->createNamed(
            $this->mapserie['name'],
            EntityType::class,
            $mapserie,
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

        if(!$this->mapserie['getMethod']?? false){
            $this->addMapserieForm($event->getForm(), null);
            return;
        }

        $mapserieorNull   = $data->{$this->mapserie['getMethod']}() ?? null;

        $this->addMapserieForm($event->getForm(), $mapserieorNull);
    }

    /**
     * @param FormEvent $event
     */
    public function preSubmit(FormEvent $event)
    {
        $data = $event->getData();
        if (null === $data) return;

        $mapserieorNull = $data[$this->mapserie['name']] ?? null;

        $this->addMapserieForm($event->getForm(), $mapserieorNull);
    }
}