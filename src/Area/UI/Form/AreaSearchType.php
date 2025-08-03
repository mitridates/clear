<?php
namespace App\Area\UI\Form;
use Symfony\Component\{Form\AbstractType, Form\FormBuilderInterface, OptionsResolver\OptionsResolver};

class AreaSearchType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $factory = $builder->getFormFactory();

        $fields= new AreaFields();
        foreach ($fields->getNamedFields(['name', 'code', 'mapsheet', ]) as $field){
            $field[2]['required']= false;
            call_user_func_array([$builder, 'add'], $field);
        }
        $fields->addAdministrativeDivisionsEventSubscriber(
            $factory,
            $builder,
            ['field'=>'country','options'=>['options'=>['required' => false]]],
            ['field'=>'admin1','options'=>['options'=>['required' => false]]]
        );
    }

    /** @inheritDoc */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'attr'=> ['id'=>(new \ReflectionClass($this))->getShortName().rand()]
        ));
    }
}
