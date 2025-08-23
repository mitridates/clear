<?php
namespace App\Geonames\UI\Form\EventSubscriber;

use App\Geonames\Domain\Entity\Admin2;
use App\Geonames\Domain\Entity\Admin3;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

/**
 * Admin3  Events
 * @package App\EventListener\Form
 *
 * @example
 *
 * $admin3 = array(
 *      'name'=>'admin3',//property/field name
 *      'getMethod'=>'getAdmin3',//Get method for property if mapped
 *      'options'=>array() //ChoiceType Field options
 *  );
 *
 * $builder->addEventSubscriber(new AddAdmin3FieldSubscriber($factory, $admin2, $admin3) )
 */
class Admin3FieldSubscriber implements EventSubscriberInterface
{
    /**
     * @var FormFactoryInterface
     */
    private $factory;
    /**
     * @var array
     */
    private $admin2;
    /**
     * @var array
     */
    private $admin3;


    public function __construct(FormFactoryInterface $factory, array $admin2=[], array $admin3=[])
    {
        $this->factory = $factory;
        $this->admin2  = $admin2;
        $this->admin3= array_replace_recursive(
            array(
                'name'=>'admin3',//field name/property if mapped
                'getMethod'=>'getAdmin3',//method if mapped
                'default'=>null,
                'options'=>array(
                    'placeholder' => 'government.level.admin3',
                    'translation_domain' => 'cavemessages',
                    'attr'=>array(
                        'class'=>'js-admin3'//jsuggest-select
                    )
                )
            ),
            $admin3);
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array
    {
        return array(
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_SUBMIT   => 'preSubmit'
        );
    }

    /**
     * @param FormInterface $form
     * @param string|Admin2|null  $admin2
     * @param string|Admin3|null  $admin3
     */
    private function addAdmin3Form($form, $admin2, $admin3)
    {

        //Set default value. Perfect for search form
        //in data forms set parent (country, admin1, admin2) in typeForm | controller
        $def= $this->admin3['default'];
        if($def){
            if(!$admin2) $admin2 = $def->getAdmin2();
            if(!$admin3 && $def->getId()) $admin3= $def;
        }


        $options = array_merge(
                    array(
                        'class'=> Admin3::class,
                        'translation_domain' => 'cavemessages',
                        'placeholder'   => 'select.government.level.admin3',//cavemessages.yml
                        'label'=> 'government.level.admin3',//cavemessages.yml
                        'attr'          => array(),
                        'required'=>false,
                        'auto_initialize' => false,
                        'choice_label' => 'name',
                        'choice_value'=>'id',
                        'query_builder' => function (EntityRepository $repository) use ($admin2, $admin3)
                        {
                            $qb = $repository->createQueryBuilder('a3')
                                ->innerJoin('a3.admin2', 'a2')
                                ->where('a3.admin2 = :id')
                                ->setParameter(':id', $admin2)
                                ->orderBy('a3.name', 'ASC');
                            if($admin3){
                                $qb->andWhere('a3.id = :admin3')
                                    ->setParameter(':admin3', $admin3);
                            }
                            return $qb;
                        }
                    ), $this->admin3['options']
        );

        $form->add($this->factory->createNamed(
            $this->admin3['name'],
            EntityType::class,
            $admin3,
            $options));
    }

    /**
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        $data = $event->getData();
        if (null === $data) return;

        if(! $this->admin3['getMethod']?? false){
            $this->addAdmin3Form($event->getForm(), null, null);
            return;
        }

        $admin2orNull   = $data->{$this->admin2['getMethod']}() ?? null;
        $admin3orNull   = $data->{$this->admin3['getMethod']}() ?? null;

        $this->addAdmin3Form($event->getForm(), $admin2orNull, $admin3orNull);

    }

    /**
     * @param FormEvent $event
     */
    public function preSubmit(FormEvent $event)
    {
        $data = $event->getData();//array
        if (null === $data) return;

        $admin2orNull = $data[$this->admin2['name']] ?? null;
        $admin3orNull = $data[$this->admin3['name']] ?? null;
        $this->addAdmin3Form($event->getForm(), $admin2orNull,  $admin3orNull);
    }
}