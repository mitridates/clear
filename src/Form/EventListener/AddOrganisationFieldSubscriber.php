<?php
namespace App\Form\EventListener;
use App\Organisation\Domain\Entity\Organisation;
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
class AddOrganisationFieldSubscriber implements EventSubscriberInterface
{
    /**
     * @var FormFactoryInterface
     */
    private $factory;
    /**
     * @var array
     */
    private $organisation;

    public function __construct(FormFactoryInterface $factory, array $organisation=[])
    {
        $this->factory = $factory;
        $organisation['name'] = $organisation['name'] ?? 'organisation';
        $organisation['getMethod']= $organisation['getMethod'] ?? sprintf('get%s', ucfirst($organisation['name']));
        $this->organisation = array_replace_recursive(
            [//'name'=>'organisation','getMethod'=>'getOrganisation',
                'options'=>array(
                    'placeholder'   => 'select.organisation',
                    'translation_domain' => 'cavemessages',
                    'attr'=>[ 'class'=>'js-organisation jsuggest-select']
                )
            ],
            $organisation
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
     * @param string|Organisation|null $organisation
     */
    private function addOrganisationForm($form, $organisation)
    {
        $options = array_merge(
            array(
                'auto_initialize' => false,
                'attr'          => array(),
                'translation_domain' => 'cavemessages',
                'placeholder'   => 'select.organisation',
                'class'=> Organisation::class,
                'required' => false,
                'label'=>'Organisation',
//                'choice_label' => 'name',
                'choice_label' => function($data){
                    /** @var Organisation $data*/
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
                'query_builder' => function (EntityRepository $repository) use ($organisation) {
                        $qb= $repository->createQueryBuilder('o');
                        return $qb
                                //->select('o.id, o.name, o.admin1')
                                ->where('o.id = :organisation')
                                ->setParameter('organisation', $organisation);
                    }
            ),$this->organisation['options'] ?? []
        );

        $form->add($this->factory->createNamed(
            $this->organisation['name'],
            EntityType::class,
            $organisation,
            $options
        ));
    }

    /**
     * @return array
     */
    public function getOrganisation(): array
    {
        return $this->organisation;
    }

    /**
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        $data = $event->getData();
        if (null === $data) return;

        $this->addOrganisationForm($event->getForm(), $data->{$this->organisation['getMethod']}() ?? null);
    }

    /**
     * @param FormEvent $event
     */
    public function preSubmit(FormEvent $event)
    {
        $data = $event->getData();
        if (null === $data) return;

        $this->addOrganisationForm($event->getForm(), $data[$this->organisation['name']] ?? null);
    }

}