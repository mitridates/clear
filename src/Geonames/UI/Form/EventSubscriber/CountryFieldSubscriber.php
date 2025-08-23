<?php
namespace App\Geonames\UI\Form\EventSubscriber;
use App\Geonames\Domain\Entity\Country;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

/**
 * Country Events
 * @package App\EventListener\Form
 *
 * @example
 *
 * $country = array(
 *      'name'=>'countryorg',           //property/field name
 *      'getMethod'=>'getCountryorg',   //Get method for property if mapped
 *      'preferred'=>['ES'],             //prefered choice. ISO 2 or null
 *      'options'=>array()              //ChoiceType Field options
 *  );
 *
 * $builder->addEventSubscriber(new AddAdmin1FieldSubscriber($factory, $country) )
 *
 */
class CountryFieldSubscriber implements EventSubscriberInterface
{
    private FormFactoryInterface $factory;
    private array $country;

    public function __construct(FormFactoryInterface $factory, array $country=[])
    {
        $this->factory =  $factory;
        $this->country= array_replace_recursive(
            array(
                'name'=>'country',//field name/property if mapped
                'getMethod'=>'getCountry',//method if mapped
                'default'=>null,//Country||id if null value
                'options'=>array(
                    'label'=>'government.level.country',//cavemessages.yml
                    'placeholder' => 'select.government.level.country',
                    'translation_domain' => 'cavemessages',
                )
            ),
            $country);
    }

    public static function getSubscribedEvents(): array
    {
        return array(
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_SUBMIT     => 'preSubmit'
        );
    }

    /**
     * @return array
     */
    public function getCountry(): array
    {
        return $this->country;
    }

    private function addCountryForm(FormInterface $form, Country|string|null $country): void
    {
        //Set default value. Perfect for search form
        //use carefully in other case
        if(!$country && $this->country['default']){
            $country= $this->country['default'];
        }

        $options = array_merge(
            array(
                'auto_initialize' => false,
                'attr' => array(),
                'class' => Country::class,
                'required' => false,
                'translation_domain' => 'cavemessages',
                'placeholder'   => 'select.government.level.country',//cavemessages.yml
                'choice_label' => 'name',
                'choice_value'=>'id',
//                'preferred_choices'=>function ($choice, $key) use ($country) {
//                    if($country && $choice->getId() === $country->getId()) return [$choice->getId()];
//                           //return in_array($choice->getId(), is_array($preferred)? $preferred : (array)$preferred );
//                 }
                ), $this->country['options']
        );

       $form->add($this->factory->createNamed(
           $this->country['name'] ,
           EntityType::class ,
           $country,
           $options
        ));
    }

    public function preSetData(FormEvent $event): void
    {
        $data = $event->getData();
        if (null === $data) return;

        $form = $event->getForm();

        if($this->country['getMethod'] ?? false){
            $this->addCountryForm($form, NULL);
            return;
        }

        $countryOrNull = $data->{$this->country['getMethod']}() ?? null;
        $this->addCountryForm($form, $countryOrNull);
    }

    public function preSubmit(FormEvent $event): void
    {
        $data = $event->getData();
        if (null === $data) return;
        $form = $event->getForm();

        $this->addCountryForm($form, $data[$this->country['name']] ?? null);
    }
}