<?php

namespace App\Form\EventListener;

use App\Domain\Geonames\Entity\Admin1;
use App\Domain\Geonames\Entity\Country;
use App\Entity\Organisation;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

/**
 * Admin1 Events for mapped or unmapped form fields
 * @package App\EventListener\Form
 * @see https://symfony.com/doc/current/reference/forms/types/choice.html
 * @example
 *
 * $admin1 = array(
 *      'name'=>'admin1',//property/field name
 *      'getMethod'=>'getAdmin1',//Get method for property if mapped
 *      'options'=>array() //ChoiceType Field options
 *  );
 *
 * $builder->addEventSubscriber(new AddAdmin1FieldSubscriber($factory, $country, $admin1) )
 */
class AddAdmin1FieldSubscriber implements EventSubscriberInterface
{

    public function __construct(private readonly FormFactoryInterface $factory, private readonly array $country=[], private array $admin1=[])
    {
        $this->admin1= array_replace_recursive(
            array(
                'name'=>'admin1',//field name/property if mapped
                'getMethod'=>'getAdmin1',//method if mapped
                'default'=>null,//Admin1
                'options'=>array(
                    'placeholder' => 'government.level.admin1',
                    'translation_domain' => 'cavemessages',
                    'attr'=>array(
                        'class'=>'js-admin1'//jsuggest-select
                    )
                )
            ),
            $admin1);

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

    private function addAdmin1Form(FormInterface $form, string|Country|null $country, string|Admin1|null $admin1)
    {
        //Set default value. Perfect for search form
        //For data forms set parent (country) in typeForm | controller
        $def= $this->admin1['default'];

        if(!is_null($admin1) && $def){
            if($def->getCountry()) $country= $def->getCountry();
            if($def->getId()) $admin1 = $def;
        }

        $options = array_merge(
                array(
                'auto_initialize' => false,
                'attr'          => array(),
                'class'         => Admin1::class,
                'required'      => false,
                'translation_domain' => 'cavemessages',
                'placeholder'   => 'select.government.level.admin1',
                'label'=>'government.level.admin1',//cavemessages.yml
                'choice_label' => 'name',
                'choice_value'=>'id',
                'query_builder' =>
                        function (EntityRepository $repository) use ($country, $admin1) {

                            $qb = $repository->createQueryBuilder('admin1');

                            $qb->innerJoin('admin1.country', 'country')
                                ->where('admin1.country = :country')
                                ->orderBy('admin1.name', 'ASC')
                                ->setParameter(':country', $country);

                            if(!is_null($admin1)){
                                $qb->andWhere('admin1.id = :admin1')
                                    ->setParameter(':admin1', $admin1);
                            }
                            return $qb;
                    }
                ), $this->admin1['options']
            );

        $form->add($this->factory->createNamed(
            $this->admin1['name'],
            EntityType::class,
            $admin1,
            $options
        ));

    }

    public function getCountry(): array
    {
        return $this->country;
    }

    public function getAdmin1(): array
    {
        return $this->admin1;
    }

    public function preSetData(FormEvent $event): void
    {

        $data = $event->getData();
        $form = $event->getForm();

        if (null === $data || ($data instanceof Organisation && $data->getId()===null)){
            $this->addAdmin1Form($form, NULL, NULL);
            return;
        }


        if(! $this->admin1['getMethod']?? false){
            $this->addAdmin1Form($form, NULL, NULL);
            return;
        }

        $countryorNull = $data->{$this->country['getMethod']}() ?? null;
        $admin1orNull = (null!== $this->admin1) ? $data->{$this->admin1['getMethod']}() : null;
        $this->addAdmin1Form($form, $countryorNull, $admin1orNull);
    }

    public function preSubmit(FormEvent $event): void
    {
        $data = $event->getData();//array

        if (null === $data) return;

        $form = $event->getForm();
        $countryorNull = $data[$this->country['name']] ?? null;
        $admin1orNull =  $data[$this->admin1['name']] ?? null;

        $this->addAdmin1Form($form, $countryorNull, $admin1orNull);
    }
}