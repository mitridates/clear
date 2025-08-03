<?php

namespace App\SystemParameter\UI\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SystemParameterType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $factory = $builder->getFormFactory();
        $fields= new SystemParameterFields($options);
        foreach ($fields->getFields() as $field) {
            call_user_func_array([$builder, 'add'], $field);
        }
        foreach ($fields->getSubscribers() as $s) {
            $builder->addEventSubscriber(new $s[1]($factory, $s[2]));
        }
  }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'locales'=>[],
            'attr'=> ['id'=>'form_id_'.rand()],
        ));
    }
}


