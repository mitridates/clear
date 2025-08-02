<?php
namespace App\Form\EventListener;
use App\Domain\Link\Entity\Link;
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
class AddLinkFieldSubscriber implements EventSubscriberInterface
{
    /**
     * @var FormFactoryInterface
     */
    private $factory;
    /**
     * @var array
     */
    private $link;

    public function __construct(FormFactoryInterface $factory, array $link=[])
    {
        $this->factory = $factory;
        $link['name'] = $link['name'] ?? 'link';
        $link['getMethod']= $link['getMethod'] ?? sprintf('get%s', ucfirst($link['name']));

        $this->link = array_replace_recursive(
            [
                'options'=>array(
                    'placeholder'   => 'select.link',
                    'translation_domain' => 'cavemessages',
                    'attr'=>[ 'class'=>'js-link jsuggest-select']
                )
            ],
            $link
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
     * @param string|Link|null $link
     */
    private function addLinkForm($form, $link)
    {
        $options = array_merge(
            array(
                'auto_initialize' => false,
                'attr'          => array(),
                'translation_domain' => 'cavemessages',
                'placeholder'   => 'select.link',
                'class'=> Link::class,
                'required' => false,
                'label'=>'Link',
                'choice_label' => function ($link) {
                    return $link->getTitle();
                },
                'choice_value'=>'id',
                'query_builder' => function (EntityRepository $repository) use ($link) {
                    return $repository->createQueryBuilder('m')
                           ->where('m.id = :link')
                          ->setParameter('link', $link);
                    }

            ),$this->link['options'] ?? []
        );

        $form->add($this->factory->createNamed(
            $this->link['name'],
            EntityType::class,
            $link,
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
        $this->addLinkForm($event->getForm(), $data->{$this->link['getMethod']}() ?? null);
    }

    /**
     * @param FormEvent $event
     */
    public function preSubmit(FormEvent $event)
    {
        $data = $event->getData();
        if (null === $data) return;

        $this->addLinkForm($event->getForm(), $data[$this->link['name']] ?? null);
    }
}