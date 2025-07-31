<?php
namespace App\Form\EventListener;
use App\Domain\Area\Entity\Area;
use App\Domain\Geonames\Entity\Admin1;
use App\Domain\Geonames\Entity\Country;
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
 *
 * $builder->addEventSubscriber(new AddAreaFieldSubscriber( $factory, $country, array(
 *          'name'=>'area',//property/field name
 *          'getMethod'=>'getArea',//Get method for property if mapped
 *          'options'=>array() //ChoiceType Field options
 *      )) );
 */
class AddAreaFieldSubscriber implements EventSubscriberInterface
{
    /**
     * @var FormFactoryInterface 
     */
    private $factory;
    /**
     * @var array
     */
    private array $country;
    /**
     * @var array
     */
    private array $admin1;
    /**
     * @var array
     */
    private array $area;
    
    public function __construct(FormFactoryInterface $factory, array $country=[], ?array $admin1=[], array $area=[])
    {
        $this->factory = $factory;
        $this->country = $country;
        $this->admin1 = $admin1;

        $this->area= array_replace_recursive(
            array(
                'name'=>'area',//field name/property if mapped
                'getMethod'=>'getArea',//method if mapped
                'default'=>null,
                'options'=>array(
                    'placeholder' => 'select.area',
                    'translation_domain' => 'cavemessages',
                    'attr'=>array(
                        'class'=>'js-area'
                    )
                )
            ),
            $area);
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array
    {
        return array(
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_SUBMIT     => 'preSubmit'
        );
    }

    /**
     * @param FormInterface $form
     * @param string|Country|null $country
     * @param string|Admin1|null $admin1
     * @param string|Area|null $area
     */
    private function addAreaForm($form, $country, $admin1, $area)
    {
        //Set default value. Perfect for search form
        //in data forms set parent (country, admin1) in typeForm | controller
        $def= $this->area['default'];
        if(!$area && $def){
            if(!$country) $country= $def->getCountry();
            if(!$admin1) $admin1= $def->getAdmin1();
            if($def->getId()) $area = $def;
        }
//        var_dump($area);

        $options = array_merge(
                    array(
                    'auto_initialize' => false,
                    'class'         => Area::class,
                    'required'      => false,
                    'translation_domain' => 'cavemessages',
                    'label'=> 'area',//cavemessages.yml
                    'choice_label' => 'name',
                    'choice_attr' => function($area){
                        /** @var Area $area*/
                        return $area->getComment()? ['title' => $area->getComment()]: [];
                    },
                    'choice_value'=>'id',
                    'query_builder' => function (EntityRepository $repository) use ($country, $admin1, $area) {
                         $qb = $repository->createQueryBuilder('area');
                        if(!$country) return $qb;
                        if($country){
                             $qb->innerJoin('area.country', 'country')
                                 ->where('area.country = :country')
                                 ->setParameter(':country', $country);
                         }
                        if($admin1){
                            $qb ->innerJoin('area.admin1', 'admin1')
                                ->andWhere('area.admin1 = :admin1')
                                ->setParameter(':admin1', $admin1);
                        }

                        if($area){
                            $qb->andWhere('area.id = :area')
                                ->setParameter(':area', $area);
                        }
                        return $qb;
                    }
                ), $this->area['options']
                );

        $form->add($this->factory->createNamed(
            $this->area['name'],
            EntityType::class,
            $area,
            $options
        ));
    }

    /**
     * @return array
     */
    public function getCountry(): array
    {
        return $this->country;
    }


    /**
     * @return array
     */
    public function getArea(): array
    {
        return $this->area;
    }

    /**
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        $data = $event->getData();
        if (null === $data) return;
        $form = $event->getForm();

        if(! $this->area['getMethod']?? false){
            $this->addAreaForm($form, NULL, NULL);
            return;
        }

        $countryOrNull = $data->{$this->country['getMethod']}() ?? null;
        $admin1OrNull = $data->{$this->admin1['getMethod']}() ?? null;
        $areaOrNull = ($this->area) ? $data->{$this->area['getMethod']}() : null;

        $this->addAreaForm($form, $countryOrNull, $admin1OrNull, $areaOrNull);
    }

    /**
     * @param FormEvent $event
     */
    public function preSubmit(FormEvent $event)
    {
        $data = $event->getData();
        if (null === $data) return;

        $form = $event->getForm();
        $countryOrNull = $data[$this->country['name']] ?? null;
        $admin1OrNull = $data[$this->admin1['name']] ?? null;
        $areaOrNull =  $data[$this->area['name']] ?? null;

        $this->addAreaForm($form, $countryOrNull, $admin1OrNull, $areaOrNull);
    }
}