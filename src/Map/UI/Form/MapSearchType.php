<?php
namespace App\Map\UI\Form;
use App\Map\UI\Form\FormTypeFields\MapFields;
use Symfony\Component\{Form\AbstractType, Form\FormBuilderInterface, OptionsResolver\OptionsResolver};

class MapSearchType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @throws \Exception
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $factory = $builder->getFormFactory();

        $fields= new MapFields();
        foreach ($fields->getNamedFields(['name', 'type', 'sourcetype', ]) as $field){
          //  $field[2]['required']= false;
            call_user_func_array([$builder, 'add'], $field);
        }
        $ret = $fields->addAdministrativeDivisionsEventSubscriber(
            $factory,
            $builder,
            ['field'=>'country','options'=>['options'=>['required' => false]]],
            ['field'=>'admin1','options'=>['options'=>['required' => false]]],
            ['field'=>'admin2','options'=>['options'=>['required' => false]]],
            ['field'=>'admin3','options'=>['options'=>['required' => false]]]
        );


        $fields->addAdAreaEventSubscriber(
            $factory,
            $builder,
            'area', $ret['country']->getCountry(), $ret['admin1']->getAdmin1()
        );

        $s= $fields->getCountry($factory, 'sourcecountry');
        $builder->addEventSubscriber($s);

        $builder->addEventSubscriber($fields->getSubscriber($factory, 'mapserie'));
        $builder->addEventSubscriber($fields->getSubscriber($factory, 'sourceorg'));

    }

    /** @inheritDoc */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'attr'=> ['id'=>(new \ReflectionClass($this))->getShortName().rand()]
        ));
    }
}
