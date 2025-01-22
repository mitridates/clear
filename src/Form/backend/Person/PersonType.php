<?php
namespace App\Form\backend\Person;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PersonType extends AbstractType
{
    /** @inheritDoc*/
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $factory = $builder->getFormFactory();
        $fields= new PersonFields();
        foreach ($fields->getFields() as $field){
            call_user_func_array([$builder, 'add'], $field);
        }
        $fields->addAdministrativeDivisionsEventSubscriber(
            $factory,
            $builder,
            ['field'=>'country','options'=>['options'=>['required' => false]]],
            ['field'=>'admin1','options'=>['options'=>['required' => false]]],
            ['field'=>'admin2','options'=>['options'=>['required' => false]]],
            ['field'=>'admin3','options'=>['options'=>['required' => false]]]
        );

        $builder->addEventSubscriber($fields->getSubscriber($factory, 'organisation'));
        $builder->addEventSubscriber($fields->getSubscriber($factory, 'organisation2'));
        $builder->addEventSubscriber($fields->getSubscriber($factory, 'organisation3'));
    }

    /** @inheritDoc*/
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'attr'=> ['id'=>(new \ReflectionClass($this))->getShortName().rand()]
        ));
    }
}


