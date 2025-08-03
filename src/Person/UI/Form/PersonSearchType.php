<?php
namespace App\Person\UI\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PersonSearchType extends AbstractType
{
    /** @inheritDoc*/
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $factory = $builder->getFormFactory();
        $fields= new PersonFields();
        $fields->addAdministrativeDivisionsEventSubscriber(
            $factory,
            $builder,
            ['field'=>'country','options'=>['options'=>['required' => false]]],
            ['field'=>'admin1','options'=>['options'=>['required' => false]]],
            ['field'=>'admin2','options'=>['options'=>['required' => false]]],
            ['field'=>'admin3','options'=>['options'=>['required' => false]]]
        );

        $builder->addEventSubscriber($fields->getSubscriber($factory, 'organisation'));

        foreach ($fields->getNamedFields(['name', 'surname']) as $field){
            $field[2]['required']= false;
            call_user_func_array([$builder, 'add'], $field);
        }
    }

    /** @inheritDoc*/
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'attr'=> ['id'=>(new \ReflectionClass($this))->getShortName().rand()]
        ));
    }
}
